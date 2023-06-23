<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Entity\Categorie;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Form\QuestionFormType;
use DateTime;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\CategorieRepository;
use App\Repository\ReponseRepository;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\Form\FormFactoryInterface;
use App\Form\EditCategorieType;
use App\Form\EditQuestionType;
use App\Form\EditReponseType;


class QuizzController extends AbstractController
{
    private $entityManager;

    public function __construct(Connection $connection, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->connection = $connection;
    }

    #[Route('/quizz', name: 'quizz', requirements: ['_isAdmin' => true])]
    public function listeQuizz(CategorieRepository $categorieRepository): Response
    {
        $quizz = $categorieRepository->findBy(['disabled' => false]);

        return $this->render('categories/liste.html.twig', [
            'controller_name' => 'QuizzController',
            'quizz' => $quizz,
        ]);
    }


    #[Route('/quizz_stat', name: 'quizz_stat', requirements: ['_isAdmin' => true])]
    public function statQuizz(CategorieRepository $categorieRepository): Response
    {
        $quizz = $categorieRepository->findBy(['disabled' => false]);

        $quizzStats = [];
        foreach ($quizz as $quiz) {
            $participations = $this->calculateParticipations($quiz);
            $averageScore = $this->calculateAverageScore($quiz);
            $questionCount = $this->calculateQuestionCount($quiz);

            $quizzStats[] = [
                'quizz' => $quiz,
                'participations' => $participations,
                'averageScore' => $averageScore,
                'questionCount' => $questionCount,
            ];
        }

        return $this->render('categories/stats.html.twig', [
            'controller_name' => 'QuizzController',
            'quizzStats' => $quizzStats,
        ]);
    }

    private function calculateParticipations(Categorie $quizz): int
    {
        $quizzId = $quizz->getId();
        $query = $this->connection->createQueryBuilder()
            ->select('COUNT(a.id)')
            ->from('activite', 'a')
            ->where('a.categorie_id = :quizzId')
            ->setParameter('quizzId', $quizzId);

        return $query->execute()->fetchOne();
    }

    private function calculateAverageScore(Categorie $quizz): float
    {
        $quizzId = $quizz->getId();
        $query = $this->connection->createQueryBuilder()
            ->select('AVG(a.score)')
            ->from('activite', 'a')
            ->where('a.categorie_id = :quizzId')
            ->setParameter('quizzId', $quizzId);

        return (float) $query->execute()->fetchOne();
    }

    private function calculateQuestionCount(Categorie $quizz): int
    {
        $quizzId = $quizz->getId();
        $query = $this->connection->createQueryBuilder()
            ->select('COUNT(q.id)')
            ->from('question', 'q')
            ->where('q.categorie_id = :quizzId')
            ->setParameter('quizzId', $quizzId);

        return $query->execute()->fetchOne();
    }

    #[Route('/liste_questions/{quizzId}', name: 'liste_questions', requirements: ['_isAdmin' => true])]
    public function listeQuestion(CategorieRepository $categorieRepository, QuestionRepository $questionRepository, $quizzId): Response
    {
        $quizz = $questionRepository->findBy(['categorie' => $quizzId]);
        $categorie = $categorieRepository->find($quizzId);

        return $this->render('categories/questions.html.twig', [
            'controller_name' => 'QuizzController',
            'quizz' => $quizz,
            'categorie' => $categorie,
        ]);
    }

    #[Route('/liste_reponses/{questionId}', name: 'liste_reponses', requirements: ['_isAdmin' => true])]
    public function listeReponses(ReponseRepository $reponseRepository, QuestionRepository $questionRepository, $questionId): Response
    {
        $responses = $reponseRepository->findBy(['question' => $questionId]);
        $question = $questionRepository->find($questionId);
    
        return $this->render('categories/reponses.html.twig', [
            'controller_name' => 'QuizzController',
            'responses' => $responses,
            'question' => $question,
        ]);
    }

    #[Route('/edit_categorie/{quizzId}', name: 'edit_categorie', requirements: ['_isAdmin' => true])]
    public function editCategorie(Request $request, EntityManagerInterface $entityManager, int $quizzId, FormFactoryInterface $formFactory): Response
    {
        $categorieRepository = $entityManager->getRepository(Categorie::class);
        $categorie = $categorieRepository->find($quizzId);
    
        if (!$categorie) {
            throw $this->createNotFoundException('Category not found');
        }
    
        $form = $formFactory->create(EditCategorieType::class, $categorie);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle the form submission and update the category entity
    
            // Persist and flush the changes
            $entityManager->flush();
    
            // Redirect or render a success message
            return $this->redirectToRoute('edit_categorie', ['quizzId' => $categorie->getId()]);
        }
    
        return $this->render('categories/edit_categorie.html.twig', [
            'controller_name' => 'QuizzController',
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/edit_question/{questionId}', name: 'edit_question', requirements: ['_isAdmin' => true])]
    public function editQuestion(Request $request, Question $questionId): Response
    {
        $form = $this->createForm(EditQuestionType::class, $questionId);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Perform question update operations

            return $this->redirectToRoute('quizz');
        }

        return $this->render('categories/edit_question.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit_reponse/{reponseId}', name: 'edit_reponse', requirements: ['_isAdmin' => true])]
    public function editReponse(Request $request, Reponse $reponseId): Response
    {
        $form = $this->createForm(EditReponseType::class, $reponseId);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Perform response update operations

            return $this->redirectToRoute('quizz');
        }

        return $this->render('categories/edit_reponse.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    
    #[Route('/delete_reponse/{reponseId}', name: 'delete_reponse', requirements: ['_isAdmin' => true])]
    public function deleteReponse(ReponseRepository $reponseRepository, EntityManagerInterface $entityManager, $reponseId): Response
    {
        $reponse = $reponseRepository->find($reponseId);
    
        if (!$reponse) {
            return $this->redirectToRoute('quizz');
        }
    
        $entityManager->remove($reponse);
        $entityManager->flush();

        return $this->redirectToRoute('quizz');
    }

    #[Route('/delete_question/{questionId}', name: 'delete_question', requirements: ['_isAdmin' => true])]
    public function deleteQuestion(QuestionRepository $questionRepository, ReponseRepository $reponseRepository, EntityManagerInterface $entityManager, $questionId): Response
    {
        $question = $questionRepository->find($questionId);
    
        if (!$question) {
            return $this->redirectToRoute('quizz');
        }
    
        $responses = $reponseRepository->findBy(['question' => $questionId]);
    
        foreach ($responses as $response) {
            $entityManager->remove($response);
        }
    
        $entityManager->remove($question);
        $entityManager->flush();

        return $this->redirectToRoute('quizz');
    }
    

    #[Route('/delete_quizz/{quizzId}', name: 'delete_quizz', requirements: ['_isAdmin' => true])]
    public function deleteQuizz(EntityManagerInterface $entityManager, CategorieRepository $categorieRepository, $quizzId): Response
    {
        $categorieRepository = $entityManager->getRepository(Categorie::class);
        $quizz = $categorieRepository->find($quizzId);

        if ($quizz) {
            $quizz->setDisabled(true);
            $entityManager->flush();
        }

        return $this->redirectToRoute('quizz');

    }

}
