<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Form\FigureFormType;
use App\Form\UserFormType;
use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Twig\Environment;

class TricksController extends AbstractController
{
    /**
     * @Route("/tricks", name="tricks_list")
     */
    public function showAllTricks(
        Request $request,
        EntityManagerInterface $entityManager,
        FigureRepository $figureRepository,
        SluggerInterface $slugger
    ): Response {
        $figure = new Figure();

        $form = $this->createForm(FigureFormType::class, $figure);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $verification = in_array('ROLE_ADMIN', $user->getRoles());

            $figure->setCertified($verification);
            $figure->setUser($user);
            $figure->setSlug(
                $slugger->slug($figure->getName())->lower()
            );
            $entityManager->persist($figure);
            $entityManager->flush();


            return $this->redirectToRoute("trick_show", array('slug' => $figure->getSlug()));
        }

        return $this->render('tricks/list.html.twig', [
            'title' => 'Snow Tricks',
            'figures' => $figureRepository->findBy(array(), array('dateCreation' => 'DESC')),
            'figure_form' => $form->createView()
        ]);
    }

    /**
     * @Route("/tricks/{slug}", name="trick_show")
     */
    public function show(
        Request $request,
        string $slug,
        EntityManagerInterface $entityManager,
        CommentRepository $commentRepository
    ): Response {
        $repository = $entityManager->getRepository(Figure::class);
        $figure = $repository->findOneBy(array('slug' => $slug));

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($user);
            $comment->setFigure($figure);
            $entityManager->persist($comment);
            $entityManager->flush();


            return $this->redirectToRoute("trick_show", array('slug' => $slug));
        }


        return $this->render('tricks/show.html.twig', [
            'figure' => $figure,
            'comment_form' => $form->createView(),
            'comments' => $commentRepository->findBy(['figure' => $figure], ['dateCreation' => 'DESC'])
        ]);
    }

    /**
     * @Route("/delete/figure/{id}", name="delete_figure")
     */
    public function deleteFigure(EntityManagerInterface $entityManager, int $id, Request $request): Response
    {
        $repository = $entityManager->getRepository(Figure::class);
        $figure = $repository->findOneBy(array('id' => $id));

        $user = $this->getUser();

        if ($user == $figure->getUser()) {
            $entityManager->remove($figure);
            $entityManager->flush();
        } else {
            throw new \Exception("Vous n'avez pas les permissions pour effectuer cette action.");
        }
        $route = $request->headers->get('referer');
        return $this->redirect($route);
    }

    /**
     * @Route("/edit/figure/{id}", name="edit_figure")
     */
    public function editComment(
        Request $request,
        int $id,
        EntityManagerInterface $entityManager
    ): Response {
        $repository = $entityManager->getRepository(Figure::class);
        $figure = $repository->findOneBy(array('id' => $id));

        $user = $this->getUser();

        if ($user == $figure->getUser()) {
            $editFigureForm = $this->createForm(FigureFormType::class, $figure);
            $editFigureForm->handleRequest($request);

            if ($editFigureForm->isSubmitted() && $editFigureForm->isValid()) {
                $figure->setDatemodif(new \DateTime());
                $entityManager->persist($figure);
                $entityManager->flush();

                return $this->redirectToRoute("trick_show", array('slug' => $figure->getSlug()));
            }
        } else {
            throw new \Exception("Vous n'avez pas les permissions pour effectuer cette action.");
        }

        return $this->render('edit-forms/edit-figure.html.twig', [
            'edit_figure_form' => $editFigureForm->createView(),
            'comment' => $figure,
        ]);
    }
}
