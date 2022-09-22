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
}
