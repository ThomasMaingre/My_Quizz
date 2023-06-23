<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Activite;
use App\Form\EditUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private $entityManager;
    private $security;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, Security $security, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @Route("/utilisateur", name="app_user_index")
     */
    public function index(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/utilisateur/moi", name="app_user_show")
     */
    public function show(): Response
    {
        $user = $this->getUser();

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/utilisateur/modifier", name="app_user_edit")
     */
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();

            if ($newPassword) {
                // Hash the new password
                $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            $this->entityManager->flush();

            return $this->redirectToRoute('app_user_show');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mes_stats", name="mon_score")
     */
    public function userActivities(SessionInterface $session): Response
    {
        $user = $this->security->getUser();
        $activities = [];
        if (!$session->isStarted()) {
            $session->start();
            $session->set('sessionId', $session->getId());
        }

        $sessionId = $session->get('sessionId');
        if ($user) {
            $userId = $user->getId();
            // Try to find activities by user ID
            if ($userId) {
                $activities = $this->entityManager->getRepository(Activite::class)->findBy(['user' => $userId], ['date' => 'DESC']);
            }
        } else {
            // Try to find activities by session ID
            $activities = $this->entityManager->getRepository(Activite::class)->findBy(['session' => $sessionId], ['date' => 'DESC']);
        }

        return $this->render('user_stat/user_stat.html.twig', [
            'controller_name' => 'UserStatController',
            'activities' => $activities,
        ]);
    }
}
