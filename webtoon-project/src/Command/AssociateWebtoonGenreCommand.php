<?php
// src/Command/AssociateWebtoonGenreCommand.php

namespace App\Command;

use App\Entity\Webtoon;
use App\Entity\Genre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:associate-webtoon-genre',
    description: 'Associe un ou plusieurs genres à un Webtoon.',
)]
class AssociateWebtoonGenreCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('webtoonId', InputArgument::REQUIRED, 'ID du Webtoon')
            ->addArgument('genres', InputArgument::IS_ARRAY, 'Nom des genres à associer (séparés par un espace)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $webtoonId = $input->getArgument('webtoonId');
        $genreNames = $input->getArgument('genres');

        $webtoon = $this->entityManager->getRepository(Webtoon::class)->find($webtoonId);
        if (!$webtoon) {
            $output->writeln("<error>Webtoon avec l’ID $webtoonId non trouvé.</error>");
            return Command::FAILURE;
        }

        $genreRepo = $this->entityManager->getRepository(Genre::class);
        foreach ($genreNames as $name) {
            $genre = $genreRepo->findOneBy(['nom' => $name]);
            if (!$genre) {
                $output->writeln("<comment>Genre \"$name\" non trouvé, ignoré.</comment>");
                continue;
            }

            $webtoon->addGenre($genre);
        }

        $this->entityManager->flush();
        $output->writeln("<info>Genres associés avec succès au Webtoon #$webtoonId.</info>");

        return Command::SUCCESS;
    }
}
