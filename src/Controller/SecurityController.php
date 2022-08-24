<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
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
    public function createUser(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if (!empty($_POST['prenom']) && !empty($_POST['nom']) &&  !empty($_POST['username']) && !empty($_POST['email'])&& !empty($_POST['password']) && !empty($_POST['password_retype']))
        {
            if ($_POST['password'] === $_POST['password_retype']) {

                $user = new User();
                $password = $_POST['password'];
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $password
                );
                $user->setEmail($_POST['email'])
                    ->setPassword($hashedPassword)
                    ->setName($_POST['nom'])
                    ->setFirstName($_POST['prenom'])
                    ->setUsername($_POST['username']);
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
