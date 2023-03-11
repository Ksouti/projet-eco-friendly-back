<?php

namespace App\Command;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FixBrokenPictureCommand extends Command
{
    protected static $defaultName = 'app:fix-broken-picture';

    private $client;
    private $entityManager;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->client = $client;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setDescription('Fixes missing or broken article picture');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $articles = $this->entityManager->getRepository(Article::class)->findAll();
        $broken = 0;
        $defaultPicture = 'https://eco-friendly.fr/assets/img/misc/default-article-illustration.png';

        foreach ($articles as $article) {
            try {
                $response = $this->client->request(
                    'GET',
                    $article->getPicture()
                );
                $response->getContent();
            } catch (Exception $e) {
                $broken++;
                $io->text(sprintf('Lien cassé ou image manquante trouvée pour l\'article "%s" (%s)', $article->getTitle(), $article->getPicture()));
                $io->progressStart(100);
                if ($defaultPicture) {
                    $article->setPicture($defaultPicture);
                    $io->progressFinish();
                }
                $this->entityManager->flush();
            }
        }

        if ($broken > 0) {
            $io->success(sprintf('Les liens cassés ou images manquantes ont été remplacés pour %d article(s)', $broken));
        } else {
            $io->success('Aucun lien cassé ou image manquante trouvé.');
        }

        return Command::SUCCESS;
    }
}
