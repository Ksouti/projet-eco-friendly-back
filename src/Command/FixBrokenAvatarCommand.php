<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FixBrokenAvatarCommand extends Command
{
    protected static $defaultName = 'app:fix-broken-avatar';

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
        $this->setDescription('Fixes missing or broken avatar images for users');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $broken = 0;

        foreach ($users as $user) {
            try {
                $response = $this->client->request(
                    'GET',
                    $user->getAvatar()
                );
                $response->getContent();
            } catch (Exception $e) {
                $broken++;
                $io->text(sprintf('Avatar cassé trouvé pour l\'utilisateur %s (%s)', $user->getUsername(), $user->getAvatar()));
                $io->progressStart(100);
                $newAvatar = 'https://eco-friendly.fr/assets/img/misc/default-avatar.png';
                if ($newAvatar) {
                    $user->setAvatar($newAvatar);
                    $io->progressFinish();
                }
                $this->entityManager->flush();
            }
        }

        if ($broken > 0) {
            $io->success(sprintf('Les avatar cassés ont été remplacés pour %d utilisateur(s)', $broken));
        } else {
            $io->success('Aucun avatar cassé trouvé.');
        }

        return Command::SUCCESS;
    }
}
