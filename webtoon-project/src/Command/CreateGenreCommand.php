<?php
// src/Command/CreateGenreCommand.php

namespace App\Command;

use App\Entity\Genre;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:create-genre',
    description: 'Crée un nouveau genre',
)]
class CreateGenreCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('nom', InputArgument::REQUIRED, 'nom du genre');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $nom = $input->getArgument('nom');

        $genre = new Genre();
        $genre->setNom($nom);

        $this->entityManager->persist($genre);
        $this->entityManager->flush();

        $output->writeln("<info>genre \"$nom\" créé avec succès !</info>");

        return Command::SUCCESS;
    }
}
