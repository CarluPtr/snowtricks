<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_home")
     */
    public function homepage(FigureRepository $figureRepository): Response
    {
        return $this->render('home/default.html.twig', [
            'title' => 'Snow Tricks',
            'figures' => $figureRepository->findBy(array(), array('dateCreation' => 'DESC'))
        ]);
    }
}
