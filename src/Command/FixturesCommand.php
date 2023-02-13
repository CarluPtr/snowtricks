<?php

namespace App\Command;

use App\Entity\Figure;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class FixturesCommand extends Command
{
    protected static $defaultName = 'app:fixtures';
    protected static $defaultDescription = 'Add a short description for your command';
    private $entityManager;
    private $passwordHasher;
    private $slugger;


    public function __construct
    (
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        SluggerInterface $slugger
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->slugger = $slugger;
        Parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    )
    : int
    {
        $user = new User();
        $user->setUsername("admin");
        $user->setEmail("admin@snowtricks.fr");
        $user->setName("Jon");
        $user->setFirstName("Doe");
        $user->setRoles(['ROLE_ADMIN']);
        $password = "password";
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $password
        );
        $user->setPassword($hashedPassword);
        $this->entityManager->persist($user);

        for ($i=0; $i < 10; $i++){
            $user = new User();
            $user->setUsername("user" . $i);
            $user->setEmail("exempleemail" . $i . "@hotmail.fr");
            $user->setName("Jean" . $i);
            $user->setFirstName("Fontaine");
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($hashedPassword);

            $figure = new Figure();
            $figure->setName("figure" . $i);
            $figure->setDescription("description" .$i);
            $figure->setSlug(
                $this->slugger->slug($figure->getName())->lower()
            );
            $figure->setCertified(0);
            $figure->setContent("Le contenu blabla numéro " . $i);
            $figure->setUser($user);
            $this->entityManager->persist($user);
            $this->entityManager->persist($figure);
        }
        $this->entityManager->flush();

        return Command::SUCCESS;
    }

}
