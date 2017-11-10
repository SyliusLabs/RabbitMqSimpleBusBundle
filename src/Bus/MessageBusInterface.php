<?php

declare(strict_types=1);

namespace SyliusLabs\RabbitMqSimpleBusBundle\Bus;

interface MessageBusInterface
{
    /**
     * @param object $message
     */
    public function handle($message): void;
}
