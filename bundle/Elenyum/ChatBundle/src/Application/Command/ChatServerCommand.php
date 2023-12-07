<?php

namespace Elenyum\ChatBundle\Application\Command;

use Elenyum\ChatBundle\Application\WebSocket\Chat;
use Elenyum\ChatBundle\Domain\Service\ChatService;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChatServerCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private ChatService $chatService;

    public function __construct(EntityManagerInterface $entityManager, ChatService $chatService)
    {
        parent::__construct();
        $this->chatService = $chatService;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('app:chat-server')
            ->setDescription('Starts the WebSocket chat server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = \React\EventLoop\Factory::create();

        $chat = new Chat($this->entityManager, $this->chatService);

        $webSocketServer = new WsServer($chat);
        $webSocketServer->enableKeepAlive($loop);

        $httpServer = new HttpServer($webSocketServer);

        $socketServer = new \React\Socket\Server('0.0.0.0:8080', $loop);

        $server = new IoServer($httpServer, $socketServer, $loop);

        $output->writeln('Server started on port 8080');
        $loop->run();

    }
}

