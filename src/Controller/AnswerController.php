<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Repository\AnswerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnswerController extends AbstractController
{
    /**
     * @Route("/answers/{id}/vote", methods="POST", name="answer_vote")
     */
    public function answerVote(Answer $answer, EntityManagerInterface $manager, LoggerInterface $logger, Request $request, $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $direction = $data['direction'] ?? 'up';



        // use real logic here to save this to the database
        if ($direction === 'up') {
            $logger->info('Voting up!');
            $answer->setVotes($answer->getVotes() + 1);
        } else {
            $logger->info('Voting down!');
            $answer->setVotes($answer->getVotes() - 1);
        }

        $manager->flush();

        return $this->json(['votes' => $answer->getVotes()]);
    }

    /**
     * @Route("/answers/popular", name="app_popular_answers")
     * @return void
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function popularAnswers(AnswerRepository $answerRepository, Request $request): Response
    {
        $answers = $answerRepository->findMostPopular($request->query->get('query'));

        return $this->render('answer/popularAnswers.html.twig', [
            'answers' => $answers
        ]);
    }
}
