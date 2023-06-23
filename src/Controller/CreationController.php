<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Question;
use App\Entity\Reponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class CreationController extends AbstractController
{

    #[Route('/creation_form_nb', name: 'nb_questions')]
    public function nb_questions(): HttpResponse
    {
        return $this->render('creation/questions.html.twig', [
            'controller_name' => 'CreationController',
        ]);
    }      
    
    #[Route('/creation_form', name: 'start_creation')]
    public function index(Request $request): HttpResponse
    {
        $question = $request->get('nb_questions');
        return $this->render('creation/index.html.twig', [
            'question' => $question,
            'controller_name' => 'CreationController',
        ]);
    }

    
    #[Route('/create_quizz', name: 'create_quizz', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): HttpResponse
    {
        $categoryName = $request->get('category');
        $category = new Categorie();
        $category->setName($categoryName);
        $entityManager->persist($category);
        
        $questions = $request->get('questions');
        $answers = $request->get('answers');
        $correctAnswers = $request->get('correct_answers');

        foreach ($questions as $index => $questionText) {
            $question = new Question();
            $question->setCategorie($category);
            $question->setQuestion($questionText);
            $entityManager->persist($question);
            $correctAnswerIndex = $correctAnswers[$index];
            
            $i=0;
            foreach ($answers[$index] as $answerText) {
                $answer = new Reponse();
                $answer->setQuestion($question);
                $answer->setReponse($answerText);
                $answer->setReponseExpected($correctAnswerIndex==$i);
                $entityManager->persist($answer);
                $i++;
            }
            
        }
        
        $entityManager->flush();
        
        return $this->redirectToRoute('home');
    }
}
