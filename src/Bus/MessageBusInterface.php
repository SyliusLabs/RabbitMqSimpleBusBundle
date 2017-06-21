<?php

declare(strict_types=1);

namespace SyliusLabs\RabbitMqSimpleBusBundle\Bus;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface MessageBusInterface
{
    /**
     * @param object $message
     */
    public function handle($message): void;
}
