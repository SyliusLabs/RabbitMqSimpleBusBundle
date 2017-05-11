<?php

namespace SyliusLabs\RabbitMqSimpleBusBundle\Bus;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface MessageBusInterface
{
    /**
     * @param object $message
     */
    public function handle($message);
}
