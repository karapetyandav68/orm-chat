<?php
namespace Elenyum\ChatBundle\Application\WebSocket;

use Elenyum\ChatBundle\Domain\Service\ChatService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

class Chat implements MessageComponentInterface
{
    private SplObjectStorage $clients;
    private $entityManager;

    public function __construct(
        EntityManagerInterface       $entityManager,
        private readonly ChatService $chatService
    )
    {
        $this->clients = new SplObjectStorage();
        $this->entityManager = $entityManager;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            $data = json_decode($msg, true);

            if (isset($data['image']) && !empty($data['image'])) {
                $this->chatService->saveImage($data['image']);
            }
            $this->chatService->saveMessage($msg, $data['image']);

            foreach ($this->clients as $client) {
                if ($from !== $client) {
                    $client->send($msg);
                }
            }
        } catch (Exception $exception) {
            // TODO add logs
            throw $exception;
        }

    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}