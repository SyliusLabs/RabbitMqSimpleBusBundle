<?php

declare(strict_types=1);

namespace SyliusLabs\RabbitMqSimpleBusBundle\Bus;

use SimpleBus\Message\Bus\MessageBus;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class SimpleBus implements MessageBusInterface
{
    /**
     * @var MessageBus
     */
    private $messageBus;

    /**
     * @param MessageBus $messageBus
     */
    public function __construct(MessageBus $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($message): void
    {
        $this->messageBus->handle($message);
    }
}
