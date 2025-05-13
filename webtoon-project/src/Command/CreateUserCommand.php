<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Console\Input\InputArgument;


#[AsCommand(
    name: 'app:create-user',
    description: 'Crée un utilisateur avec email et mot de passe encodé.',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $random_id = random_int(1, 1000);
        $user->setEmail('test_' . $random_id.'@example.com');

        $user->setPseudo('NomTest2'); 

        $hashedPassword = $this->hasher->hashPassword($user, 'password123');
        $user->setPassword($hashedPassword);

        $user->setRoles(['ROLE_USER']);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('✅ Utilisateur créé avec succès !');
        return Command::SUCCESS;
    }
}