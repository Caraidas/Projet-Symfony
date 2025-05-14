<?php
// src/Command/CreateUserWithArgCommand.php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(
    name: 'app:create-userwitharg',
    description: 'Créer un nouvel utilisateur.',
)]
class CreateUserWithArgCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $hasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'email')
            ->addArgument('pseudo', InputArgument::REQUIRED, 'pseudo')
            ->addArgument('role', InputArgument::REQUIRED, 'role');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $email = $input->getArgument('email');
        $pseudo = $input->getArgument('pseudo');
        $role = $input->getArgument('role');
        $hashedPassword = $this->hasher->hashPassword($user, 'password123');

       
        $user->setEmail($email);
        $user->setPseudo($pseudo);
        $user->setRoles([$role]);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln("<info>user \"$pseudo\" créé avec succès !</info>");

        return Command::SUCCESS;
    }
}