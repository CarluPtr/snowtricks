<?php

namespace App\Controller;

use App\Entity\Figure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class TricksController extends AbstractController
{

    /**
     * @Route("/tricks", name="tricks_list")
     */
    public function showAllTricks(EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Figure::class);
        $figures = $repository->findAll();

        return $this->render('tricks/list.html.twig', [
            'title' => 'Snow Tricks',
            'figures' => $figures,
            'randomclass' => rand(1, 2),
        ]);
    }
    /**
     * @Route("/create", name="test")
     */
    public function new(EntityManagerInterface $entityManager){
        $figure = new Figure();
        $figure->setName('360 Backflip')
            ->setDescription('La meilleure figure du monde')
            ->setSlug(
                strtolower(
                    str_replace(
                ' ', '-', $figure->getName()
                    )
                )
            );
        $entityManager->persist($figure);
        $entityManager->flush();

        return new Response(sprintf(
            'Well hallo! The shiny new question is id #%s, slug: %s',
            $figure->getName(),
            $figure->getSlug()
        ));
    }

    /**
     * @Route("/tricks/{slug}", name="app_question_show")
     */
    public function show($slug)
    {
        $answers = [
            'Make sure your cat is sitting purrrfectly still 🤣',
            'Honestly, I like furry shoes better than MY cat',
            'Maybe... try saying the spell backwards?',
        ];

        return $this->render('question/show.html.twig', [
            'question' => ucwords(str_replace('-', ' ', $slug)),
            'answers' => $answers,
        ]);
    }
}