<?php

declare(strict_types=1);

namespace Elenyum\ChatBundle\Domain\Service;

use Elenyum\ChatBundle\Domain\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;

class ChatService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function saveMessage($msg, $imageName): void
    {
        // Create a new Message entity
        $message = new Message();

        // Save the Message entity to the database
        $message->setContent($msg);
        $message->setImage($imageName);
        $message->setCreatedAt(new \DateTime());

        // Persist the Message entity to the database
        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }

    public function saveImage(string $base64ImageData): string
    {
        // Implement logic to save the base64-encoded image data to a file
        // You may use Symfony's Filesystem component or any other method

        $imageName = uniqid('image_') . '.png';
        $imagePath = 'public/uploads/' . $imageName;

        file_put_contents($imagePath, base64_decode($base64ImageData));

        return $imageName;
    }
}