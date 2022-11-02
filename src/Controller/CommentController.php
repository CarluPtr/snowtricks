<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{


    /**
     * @Route("/delete/comment/{id}", name="delete_comment")
     */
    public function deleteComment(EntityManagerInterface $entityManager, int $id, Request $request): Response
    {
        $repository = $entityManager->getRepository(Comment::class);
        $comment = $repository->findOneBy(array('id' => $id));

        $user = $this->getUser();

        if ($user == $comment->getUser()) {
            $entityManager->remove($comment);
            $entityManager->flush();
        } else {
            throw new \Exception("Vous n'avez pas les permissions pour effectuer cette action.");
        }
        $route = $request->headers->get('referer');
        return $this->redirect($route);
    }

    /**
     * @Route("/edit/comment/{id}", name="edit_comment")
     */
    public function editComment(
        Request $request,
        int $id,
        EntityManagerInterface $entityManager
    ): Response {
        $repository = $entityManager->getRepository(Comment::class);
        $comment = $repository->findOneBy(array('id' => $id));

        $user = $this->getUser();

        if ($user == $comment->getUser()) {
            $editCommentForm = $this->createForm(CommentFormType::class, $comment);
            $editCommentForm->handleRequest($request);

            if ($editCommentForm->isSubmitted() && $editCommentForm->isValid()) {
                $comment->setDatemodif(new \DateTime());
                $entityManager->persist($comment);
                $entityManager->flush();

                return $this->redirectToRoute("trick_show", array('slug' => $comment->getFigure()->getSlug()));
            }
        } else {
            throw new \Exception("Vous n'avez pas les permissions pour effectuer cette action.");
        }

        return $this->render('edit-forms/edit-comment.html.twig', [
            'edit_comment_form' => $editCommentForm->createView(),
            'comment' => $comment,
        ]);
    }
}
