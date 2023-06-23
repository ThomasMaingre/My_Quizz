<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VerificationController extends AbstractController
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @Route("/verify-email/{token}", name="email_verification")
     */
    public function verifyEmail(string $token): Response
    {
        $entityManager = $this->managerRegistry->getManager();

        // Retrieve the user based on the verification token
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['confirmation_token' => $token]);

        // Check if the user exists
        if (!$user) {
            throw $this->createNotFoundException('Invalid verification token');
        }

        // Perform the email verification process
        $now = new \DateTime();
        $user->setEmailVerifiedAt($now);

        $entityManager->persist($user);
        $entityManager->flush();

        // Redirect the user to a success page or perform any other actions

        return $this->render('verification/success.html.twig');
    }
}
