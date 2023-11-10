<?php

namespace App\Command;
use App\Chat;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'chat',
    description: 'Run the WebSocket chat server',
)]
class ChatCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDescription('Run the WebSocket chat server');
    }

        protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting WebSocket server...');

        $chat = new Chat();
        $server = IoServer::factory(
            new HttpServer(
                new WsServer($chat)
            ),
            8080
        );

        $server->run();
    }
}
