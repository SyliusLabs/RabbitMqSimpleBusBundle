<?php

declare(strict_types=1);

namespace SyliusLabs\RabbitMqSimpleBusBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use SyliusLabs\RabbitMqSimpleBusBundle\Bus\MessageBusInterface;
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
    public function execute(AMQPMessage $message): void
    {
        try {
            $denormalizedMessage = $this->denormalizer->denormalize($message);

            $this->messageBus->handle($denormalizedMessage);
        } catch (\Throwable $throwable) {
            $this->logger->error(sprintf(
                'Exception "%s" while handling an AMQP message: "%s". Stacktrace: %s',
                $throwable->getMessage(),
                $message->getBody(),
                $throwable->getTraceAsString()
            ));
        }
    }
}
