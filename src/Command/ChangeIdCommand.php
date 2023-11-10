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
    name: 'change_id',
    description: 'Change the quote by id',
)]
class ChangeIdCommand extends Command
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'The ID of the Quote to update')
            ->addArgument('text', InputArgument::REQUIRED, 'The new text for the Quote');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $id = $input->getArgument('id');
        $text = $input->getArgument('text');

        $quote = $this->entityManager->getRepository(Quote::class)->find($id);

        if (!$quote) {
            $io->error('Quote not found.');

            return Command::FAILURE;
        }
        $quote->setAuthor($text);

        $this->entityManager->flush();

        $io->success('Quote with ID ' . $id . ' updated successfully.');

        return Command::SUCCESS;
    }
}
