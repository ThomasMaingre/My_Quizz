<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Activite;
use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\EditUserType as UserType;
use Symfony\Component\Form\FormFactoryInterface;



class UserStatController extends AbstractController
{
    #[Route('/users', name: 'users', requirements: ['_isAdmin' => true])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $queryBuilder = $userRepository->createQueryBuilder('u');
        $queryBuilder->where('u.status != :disabled')
            ->setParameter('disabled', 3)
            ->orderBy('u.LastConnection', 'DESC');
        
        $users = $queryBuilder->getQuery()->getResult();
    
        return $this->render('user_stat/index.html.twig', [
            'controller_name' => 'UserStatController',
            'users' => $users,
        ]);
    }

    #[Route('/edit_user/{userId}', name: 'edit_user', requirements: ['_isAdmin' => true])]
    public function editUser(Request $request, EntityManagerInterface $entityManager, $userId, FormFactoryInterface $formFactory): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($userId);
    
        $form = $formFactory->create(UserType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle the form submission and update the user entity
    
            // Persist and flush the changes
            $entityManager->persist($user);
            $entityManager->flush();
    
            // Redirect or render a success message
            return $this->redirectToRoute('edit_user', ['userId' => $user->getId()]);
        }
    
        return $this->render('user_stat/edit.html.twig', [
            'controller_name' => 'UserStatController',
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/update_user/{userId}', name: 'update_user', methods: ['POST'], requirements: ['_isAdmin' => true])]
    public function updateUser(Request $request, EntityManagerInterface $entityManager, $userId): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        // Process the form submission
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Update the user entity with the submitted form data
            $user = $form->getData();

            // Persist the changes to the database
            $entityManager->flush();

            // Redirect to a success page or perform any other desired action
            return $this->redirectToRoute('app_user_stat');
        }

        return $this->render('user_stat/edit.html.twig', [
            'controller_name' => 'UserStatController',
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/disable_user/{userId}', name: 'disable_user', requirements: ['_isAdmin' => true])]
    public function disableUser(EntityManagerInterface $entityManager, $userId): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $user->setStatus(3);
        $entityManager->flush();

        return $this->redirectToRoute('users');
    }

    #[Route('/stats/{userId}', name: 'user_stat', methods: ['GET'], requirements: ['_isAdmin' => true])]
    public function userActivities(EntityManagerInterface $entityManager, $userId)
    {
        $activities = $entityManager->getRepository(Activite::class)->findBy(['user' => $userId], ['date' => 'DESC']);
    
        return $this->render('user_stat/user_stat.html.twig', [
            'controller_name' => 'UserStatController',
            'activities' => $activities,
        ]);
    }
    
}
