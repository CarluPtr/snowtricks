<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/delete/comment/{id}", name="delete_comment")
     */
    public function deleteComment(EntityManagerInterface $entityManager, int $id, Request $request): Response
    {

        $repository = $entityManager->getRepository(Comment::class);
        $comment = $repository->findOneBy(array('id' => $id));

        $user = $this->getUser();

        if($user == $comment->getUser()) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }
        else {
            throw new \Exception("Vous n'avez pas les permissions pour effectuer cette action.");
        }
        $route = $request->headers->get('referer');
        return $this->redirect($route);
    }
}
