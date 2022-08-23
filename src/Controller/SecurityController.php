<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/security", name="app_security")
     */
    public function index(): Response
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(): Response
    {

        return $this->render('security/register.html.twig', [

        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(): Response
    {

        return $this->render('security/login.html.twig', [

        ]);
    }
}
