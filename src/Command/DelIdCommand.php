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
    name: 'del_id',
    description: 'Delete quote by id',
)]
class DelIdCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'The ID of the Quote to delete');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $id = $input->getArgument('id');

        if ($id) {
            $io->note(sprintf('You passed an argument: %s', $id));
        }

        $quote = $this->entityManager->getRepository(Quote::class)->find($id);

        if (!$quote) {
            $io->error('Quote not found.');

            return Command::FAILURE;
        }

        $this->entityManager->remove($quote);
        $this->entityManager->flush();

        $io->success('Quote with ID ' . $id . ' deleted successfully.');

        return Command::SUCCESS;
    }
}
