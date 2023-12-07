<?php
namespace Elenyum\ChatBundle;

use Elenyum\ChatBundle\DependencyInjection\ElenyumChatExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ElenyumChatBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new ElenyumChatExtension();
    }
}