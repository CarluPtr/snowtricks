<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\User;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
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
        ]);
    }
    /**
     * @Route("/create", name="test")
     */
    public function new(EntityManagerInterface $entityManager, SluggerInterface $slugger){
        $figure = new Figure();
        $user = $this->getUser();
        $figure->setName('Super flip 180')
            ->setDescription('hrr rh rrhrdr hrrh efeg e')
            ->setSlug(
                $slugger->slug($figure->getName())->lower()
            )
            ->setUser($user);
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
