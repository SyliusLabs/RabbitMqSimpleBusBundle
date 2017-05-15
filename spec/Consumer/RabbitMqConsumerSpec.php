<?php

namespace spec\SyliusLabs\RabbitMqSimpleBusBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;
use SyliusLabs\RabbitMqSimpleBusBundle\Bus\MessageBusInterface;
use SyliusLabs\RabbitMqSimpleBusBundle\Denormalizer\DenormalizationFailedException;
use SyliusLabs\RabbitMqSimpleBusBundle\Denormalizer\DenormalizerInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class RabbitMqConsumerSpec extends ObjectBehavior
{
    function let(DenormalizerInterface $denormalizer, MessageBusInterface $messageBus, LoggerInterface $logger)
    {
        $this->beConstructedWith($denormalizer, $messageBus, $logger);
    }

    function it_is_a_oldsound_rabbitmq_bundle_consumer()
    {
        $this->shouldImplement(ConsumerInterface::class);
    }

    function it_uses_message_bus_to_dispatch_denormalized_message(
        DenormalizerInterface $denormalizer,
        MessageBusInterface $messageBus
    ) {
        $amqpMessage = new AMQPMessage('Message body');
        $denormalizedMessage = new \stdClass();

        $denormalizer->denormalize($amqpMessage)->willReturn($denormalizedMessage);

        $messageBus->handle($denormalizedMessage)->shouldBeCalled();

        $this->execute($amqpMessage);
    }

    function it_logs_exception_message_if_denormalization_fails(
        DenormalizerInterface $denormalizer,
        LoggerInterface $logger
    ) {
        $amqpMessage = new AMQPMessage('Invalid message body');

        $denormalizer->denormalize($amqpMessage)->willThrow(new DenormalizationFailedException('Message body is invalid'));

        $logger->error('Exception while handling an AMQP message: Message body is invalid')->shouldBeCalled();

        $this->execute($amqpMessage);
    }
}
