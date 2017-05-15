<?php

namespace SyliusLabs\RabbitMqSimpleBusBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use SyliusLabs\RabbitMqSimpleBusBundle\Bus\MessageBusInterface;
use SyliusLabs\RabbitMqSimpleBusBundle\Denormalizer\DenormalizationFailedException;
use SyliusLabs\RabbitMqSimpleBusBundle\Denormalizer\DenormalizerInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class RabbitMqConsumer implements ConsumerInterface
{
    /**
     * @var DenormalizerInterface
     */
    private $denormalizer;

    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param DenormalizerInterface $denormalizer
     * @param MessageBusInterface $messageBus
     * @param LoggerInterface $logger
     */
    public function __construct(
        DenormalizerInterface $denormalizer,
        MessageBusInterface $messageBus,
        LoggerInterface $logger
    ) {
        $this->denormalizer = $denormalizer;
        $this->messageBus = $messageBus;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(AMQPMessage $message)
    {
        try {
            $message = $this->denormalizer->denormalize($message);

            $this->messageBus->handle($message);
        } catch (DenormalizationFailedException $exception) {
            $this->logger->error(sprintf('Exception while handling an AMQP message: %s', $exception->getMessage()));
        }
    }
}
