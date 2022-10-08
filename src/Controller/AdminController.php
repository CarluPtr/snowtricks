<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     */
    public function deleteComment(EntityManagerInterface $entityManager, $id): Response
    {
        // Verify if user is an admin and throw AccesDeniedException if he's not
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $repository = $entityManager->getRepository(Comment::class);
        $comment = $repository->findOneBy(array('id' => $id));

        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute("app_admin");
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
