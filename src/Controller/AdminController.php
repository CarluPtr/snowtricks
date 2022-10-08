<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin")
     */
    public function adminPanel(EntityManagerInterface $entityManager, CommentRepository $commentRepository, FigureRepository $figureRepository): Response
    {
        $repository = $entityManager->getRepository(Figure::class);
        $figures = $repository->findAll();

        $commentRepository = $entityManager->getRepository(Comment::class);
        $comments = $commentRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'figures' => $figureRepository->findBy(array(), array('dateCreation' => 'DESC')),
            'comments' => $commentRepository->findBy(array(), array('dateCreation' => 'DESC'))
        ]);
    }

    /**
     * @Route("/delete/figure/{id}", name="delete_figure")
     */
    public function deleteFigure(EntityManagerInterface $entityManager, $id): Response
    {
        // Verify if user is an admin and throw AccesDeniedException if he's not
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $repository = $entityManager->getRepository(Figure::class);
        $figure = $repository->findOneBy(array('id' => $id));

        $entityManager->remove($figure);
        $entityManager->flush();

        return $this->redirectToRoute("app_admin");
    }

    /**
     * @Route("/delete/comment/{id}", name="delete_comment")
     * // Fonction pour supprimer un commentaire utilisateur également.
     */
    public function deleteComment(EntityManagerInterface $entityManager, $id, Request $request): Response
    {

        $repository = $entityManager->getRepository(Comment::class);
        $comment = $repository->findOneBy(array('id' => $id));

        $user = $this->getUser();
        // Verify if user is an admin and throw AccesDeniedException if he's not
        if(in_array('ROLE_ADMIN', $user->getRoles()) or $user == $comment->getUser())
        {
            $entityManager->remove($comment);
            $entityManager->flush();
        }
        else
        {
            throw new \Exception("Vous n'avez pas les permissions pour effectuer cette action.");
        }
        $route = $request->headers->get('referer');
        return $this->redirect($route);
    }

    /**
     * @Route("/certif/figure/{id}", name="certif_figure")
     */
    public function certifFigure(EntityManagerInterface $entityManager, $id): Response
    {
        // Verify if user is an admin and throw AccesDeniedException if he's not
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $repository = $entityManager->getRepository(Figure::class);
        $figure = $repository->findOneBy(array('id' => $id));

        if($figure->isCertified())
        {
            $figure->setCertified(0);
        }
        else
        {
            $figure->setCertified(1);
        }
        $entityManager->persist($figure);
        $entityManager->flush();

        return $this->redirectToRoute("app_admin");
    }
}
