<?php
// src/Command/CreateWebtoonCommand.php

namespace App\Command;

use App\Entity\Webtoon;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:create-webtoon',
    description: 'Crée un nouveau webtoon et l\'associe à un auteur',
)]
class CreateWebtoonCommand extends Command
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
            ->addArgument('titre', InputArgument::REQUIRED, 'Titre du webtoon')
            ->addArgument('description', InputArgument::REQUIRED, 'Description du webtoon')
            ->addArgument('authorId', InputArgument::REQUIRED, 'ID de l\'utilisateur auteur');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $titre = $input->getArgument('titre');
        $description = $input->getArgument('description');
        $authorId = $input->getArgument('authorId');

        $author = $this->userRepository->find($authorId);

        if (!$author) {
            $output->writeln('<error>Auteur introuvable</error>');
            return Command::FAILURE;
        }

        if (!in_array('ROLE_AUTHOR', $author->getRoles())) {
            $output->writeln('<error>L\'utilisateur n\'a pas le rôle ROLE_AUTHOR</error>');
            return Command::FAILURE;
        }

        $webtoon = new Webtoon();
        $webtoon->setTitre($titre);
        $webtoon->setDescription($description);
        $webtoon->setUser($author);

        $this->entityManager->persist($webtoon);
        $this->entityManager->flush();

        $output->writeln("<info>Webtoon \"$titre\" créé avec succès !</info>");

        return Command::SUCCESS;
    }
}
