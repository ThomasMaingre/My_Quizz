<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LoginController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;
    private $tokenStorage;
    private $session;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        TokenStorageInterface $tokenStorage
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/login', name: 'app_login')]
    public function index(): Response
    {
        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }

    public function login(Request $request, SessionInterface $session): Response
    {

        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $firewallName = 'main'; // Replace 'main' with the actual name of your firewall
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            // Authentication failed
            return $this->redirectToRoute('app_login');
        }

        // Authentication successful
        $token = new UsernamePasswordToken($user, $firewallName);
        $this->tokenStorage->setToken($token);
        // After authenticating the user and retrieving the user ID
        $userId = $user->getId(); // Replace this with your own logic to retrieve the user ID

        $session->set('user_id', $userId);


        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
    }
}
