<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Entity\User;
use App\Entity\Question; // Import the Question entity
use App\Entity\Reponse; // Import the Reponse entity
use DateTime;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\CategorieRepository;
use App\Repository\ReponseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class CategoriesController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/categories', name: 'app_categories')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findAllCategories();

        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/{id}', name: 'app_categories_show')]
    public function category(Request $request, CategorieRepository $categorieRepository, $id, ReponseRepository $reponseRepository, SessionInterface $session): Response
    {
        $activite = new Activite();

        if (!$session->isStarted()) {
            $session->start();
            $session->set('sessionId', $session->getId());
        }

        $sessionId = $session->get('sessionId');
        $category = $categorieRepository->find($id);
        $questions = $category->getQuestions()->toArray();
        $questionId = $request->query->get('question');
        $questionArray = array_values($questions);
        $currentQuestionIndex = 0;

        if ($questionId !== null) {
            foreach ($questionArray as $index => $question) {
                if ($question->getId() == $questionId) {
                    $currentQuestionIndex = $index;
                    break;
                }
            }
        }

        $responses = [];
        foreach ($questionArray as $question) {
            $answers = $reponseRepository->findBy(['question' => $question]);
            $responses[$question->getId()] = $answers;
        }

        $userId = $session->get('user_id');
        $user = null;

        if (isset($userId)) {
            $user = $this->entityManager->getRepository(User::class)->find($userId);
        }

        $existingActivite = $this->entityManager->getRepository(Activite::class)->findOneBy([
            'user' => $user,
            'session' => $sessionId,
            'categorie' => $category,
        ]);

        if ($existingActivite) {
            $score = $existingActivite->getScore();
        } else {
            $activite->setUser($user);
            $activite->setSession($session->get('sessionId'));
            $activite->setCategorie($category);
            $activite->setScore(0);
            $activite->setDate(new DateTime());
            $this->entityManager->persist($activite);
            $this->entityManager->flush();
            $score = $activite->getScore();
        }

        return $this->render('categories/category.html.twig', [
            'category' => $category,
            'questions' => $questions,
            'questionCount' => count($questions),
            'currentQuestionIndex' => $currentQuestionIndex,
            'responses' => $responses,
            'questionId' => $questionId,
            'questionPos' => array_keys($questions),
            'user_id' => $sessionId,
            'score' => $score,
        ]);
    }

    #[Route('/category/{id}/r', name: 'app_categories_rep', methods: ['POST'])]
    #[Route('/category/{id}/r', name: 'app_categories_rep', methods: ['POST'])]
    public function reponse(
        Request $request,
        CategorieRepository $categorieRepository,
        $id,
        ReponseRepository $reponseRepository,
        SessionInterface $session
    ): Response {
        $questionId = $request->request->get('questionId');
        $reponseId = $request->request->get('responseId');
        $question = $this->entityManager->getRepository(Question::class)->find($questionId);
        $reponse = $this->entityManager->getRepository(Reponse::class)->find($reponseId);
        // Get the correct answer with response_expected = 1
        $correctAnswer = $this->entityManager->getRepository(Reponse::class)->findOneBy([
            'question' => $question,
            'reponse_expected' => 1,
        ]);

        // Perform any further processing or logic based on the retrieved question, response, and correct answer

        // Example: Update the score of the user's activity
        $userId = $session->get('user_id');
        $user = null;

        if (isset($userId)) {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $userId]);
        }

        $sessionId = $session->get('sessionId');
        $category = $categorieRepository->find($id);
        $existingActivite = $this->entityManager->getRepository(Activite::class)->findOneBy([
            'user' => $user,
            'session' => $sessionId,
            'categorie' => $category,
        ]);

        if ($existingActivite) {
            $score = $existingActivite->getScore();
        } else {
            $activite = new Activite();
            $activite->setUser($user);
            $activite->setSession($session->get('sessionId'));
            $activite->setCategorie($category);
            $activite->setScore(0);
            $activite->setDate(new DateTime());
            $this->entityManager->persist($activite);
            $this->entityManager->flush();
            $score = $activite->getScore();
        }

        // Get the reponse_expected value
        $reponseExpected = $reponse->getReponseExpected();
        if ($existingActivite && $reponse === $correctAnswer) {
            $score = $existingActivite->getScore() + 1; // Increment the score by 1
            $existingActivite->setScore($score);
            $this->entityManager->flush();
        }

        $questions = $category->getQuestions()->toArray();
        $questionArray = array_values($questions);
        $responses = [];
        foreach ($questionArray as $question) {
            $answers = $reponseRepository->findBy(['question' => $question]);
            $responses[$question->getId()] = $answers;
        }
        $currentQuestionIndex = $request->request->get('qpos');

        return $this->render('categories/reponse.html.twig', [
            'question' => $question,
            'reponse' => $reponse,
            'correctAnswer' => $correctAnswer,
            'score' => $score,
            'reponseExpected' => $reponseExpected,
            'category' => $category,
            'questions' => $questions,
            'questionCount' => count($questions),
            'responses' => $responses,
            'questionId' => $questionId,
            'questionPos' => array_keys($questions),
            'user_id' => $sessionId,
            'score' => $score,
            'currentQuestionIndex' =>  $currentQuestionIndex - 1,
        ]);
    }


    #[Route('/update_question', name: 'update_question', requirements: ['_isAdmin' => true])]
    public function updateQuestion(CategorieRepository $categorieRepository): Response
    {
        $quizz = $categorieRepository->findAll();

        return $this->render('categories/liste.html.twig', [
            'controller_name' => 'CategorieController',
            'quizz' => $quizz,
        ]);
    }

    #[Route('/delete_question', name: 'delete_question', requirements: ['_isAdmin' => true])]
    public function deleteQuestion(CategorieRepository $categorieRepository): Response
    {
        $quizz = $categorieRepository->findAll();

        return $this->render('categories/liste.html.twig', [
            'controller_name' => 'CategorieController',
            'quizz' => $quizz,
        ]);
    }
}
