<?php

namespace App\Command;
use App\Entity\Quote;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\QuoteRepository; 
use Doctrine\ORM\EntityManagerInterface;


#[AsCommand(
    name: 'see',
    description: 'See all quotes',
)]
class SeeCommand extends Command
{

    private $quoteRepository;
    private $entityManager;

    public function __construct(QuoteRepository $quoteRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->quoteRepository = $quoteRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $quotes = $this->entityManager->getRepository(Quote::class)->findAll();

        if (empty($quotes)) {
            $io->success('No quotes found.');

            return Command::SUCCESS;
        }

        $io->success('List of Quotes with their IDs:');

        foreach ($quotes as $quote) {
            $io->writeln(sprintf('<fg=green>ID:</> %d, <fg=green>Text:</> %s', $quote->getId(), $quote->getAuthor()));
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
