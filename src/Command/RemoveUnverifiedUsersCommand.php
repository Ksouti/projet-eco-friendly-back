<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveUnverifiedUsersCommand extends Command
{
    protected static $defaultName = 'app:remove-unverified-users';
    protected static $defaultDescription = 'Remove unverified users who have not confirmed their email in a specified number of days.';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addOption('days', null, InputOption::VALUE_REQUIRED, 'Remove users who have not confirmed their email in the specified number of days', 7);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get the number of days from the command line options
        // This specifies the number of days since the users were last verified
        $days = $input->getOption('days');
        // Create a new DateTime object representing the current date and time
        // This will be used to calculate the date that is $days ago
        $date = new \DateTime();
        // Subtract $days from the current date
        // This modifies the original $date object to represent the date
        $date->modify("-$days day");
        // Convert the $date object to a string in the format 'Y-m-d H:i:s'
        $dateString = $date->format('Y-m-d H:i:s');

        $userRepository = $this->entityManager->getRepository(User::class);

        $unverifiedUsers = $userRepository->createQueryBuilder('u')
            ->where('u.is_verified = false')
            ->andWhere('u.created_at <= :date')
            ->setParameter('date', $dateString)
            ->getQuery()
            ->getResult();

        foreach ($unverifiedUsers as $user) {
            $this->entityManager->remove($user);
        }
        $this->entityManager->flush();

        //Message in the console to indicate the number of users removed
        $output->writeln(sprintf('%d unverified users removed.', count($unverifiedUsers)));

        return Command::SUCCESS;
    }
}
