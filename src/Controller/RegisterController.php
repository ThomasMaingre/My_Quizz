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

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(): Response
    {
        return $this->render('register/index.html.twig', [
            'controller_name' => 'RegisterController',
        ]);
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        TokenStorageInterface $tokenStorage
    ): Response {
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $now = new \DateTime();
        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $password = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($password);
        $user->setStatus(0); // Set initial status to "0" (unverified)
        $user->setCreatedAt($now);
        $user->setUpdatedAt($now);

        // Generate a verification token
        $token = bin2hex(random_bytes(32));
        $user->setConfirmationToken($token);

        $entityManager->persist($user);
        $entityManager->flush();

        // Send confirmation email with verification link
        $subject = 'Registration Confirmation';
        $body = $this->renderView('emails/verification.html.twig', [
            'name' => $name,
            'token' => $token,
        ]);

        $this->sendEmailViaCurl($email, $subject, $body);

        return $this->redirectToRoute('home');
    }

    private function sendEmailViaCurl(string $email, string $subject, string $body): void
    {
        $headers = [];

        $command = "curl --url 'smtp://localhost:1025' --mail-from 'sender@example.com' --mail-rcpt '$email' --upload-file - <<EOF\n" .
            implode("\n", $headers) . "\n\n" .
            $body . "\nEOF";

        shell_exec($command);
    }

    #[Route('/verify/{token}', name: 'verify_account')]
    public function verifyAccount(string $token, EntityManagerInterface $entityManager): Response
    {
        $now = new \DateTime();
        $user = $entityManager->getRepository(User::class)->findOneBy(['confirmation_token' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('Invalid verification token');
        }
        if ($user->isConfirmed() == 1) {
            return new Response('Account already verify');
        }
        $user->setConfirmed(1);
        $user->setEmailVerifiedAt($now);
        $user->setStatus(1);
        $entityManager->flush();

        return new Response('Verification successful');
    }
}
