<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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
     * @Route("/create-account", name="account_creation")
     */
    public function createUser(Request $request,EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if (!empty($request->get('prenom')) && !empty($request->get('nom')) &&  !empty($request->get('username')) && !empty($request->get('email'))&& !empty($request->get('password')) && !empty($request->get('password_retype')))
        {
            if ($request->get('password') === $request->get('password_retype')) {

                $user = new User();
                $password = $request->get('password');
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $password
                );
                $user->setEmail($request->get('email'))
                    ->setPassword($hashedPassword)
                    ->setName($request->get('nom'))
                    ->setFirstName($request->get('prenom'))
                    ->setUsername($request->get('username'));
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->render('home/default.html.twig');
            } else {
                throw new Exception('Mot de passe non correspondant.');
            }
        }
        else
        {
            throw new Exception('Veuillez remplir tous les champs.');
        }
    }


    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
             return $this->redirectToRoute('main_home');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

}
