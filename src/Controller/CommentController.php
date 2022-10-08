<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/newcomment", name="comment")
     */
    public function newComment(EntityManagerInterface $entityManager){
        $comment = new Comment();
        $user = $this->getUser();
        $figure = $entityManager->getRepository(Figure::class)->find(3);
        $comment->setContent('commentaire test')
            ->setFigure($figure)
            ->setUser($user);
        $entityManager->persist($comment);
        $entityManager->flush();

        return new Response('Comment created.');
    }
}
