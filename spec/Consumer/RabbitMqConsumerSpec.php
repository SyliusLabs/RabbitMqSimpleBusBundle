<?php

declare(strict_types=1);

namespace spec\SyliusLabs\RabbitMqSimpleBusBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use SyliusLabs\RabbitMqSimpleBusBundle\Bus\MessageBusInterface;
use SyliusLabs\RabbitMqSimpleBusBundle\Denormalizer\DenormalizationFailedException;
use SyliusLabs\RabbitMqSimpleBusBundle\Denormalizer\DenormalizerInterface;

final class RabbitMqConsumerSpec extends ObjectBehavior
{
    function let(DenormalizerInterface $denormalizer, MessageBusInterface $messageBus, LoggerInterface $logger): void
    {
        $this->beConstructedWith($denormalizer, $messageBus, $logger);
    }

    function it_is_a_oldsound_rabbitmq_bundle_consumer(): void
    {
        $this->shouldImplement(ConsumerInterface::class);
    }

    function it_uses_message_bus_to_dispatch_denormalized_message(
        DenormalizerInterface $denormalizer,
        MessageBusInterface $messageBus
    ): void {
        $amqpMessage = new AMQPMessage('Message body');
        $denormalizedMessage = new \stdClass();

        $denormalizer->denormalize($amqpMessage)->willReturn($denormalizedMessage);

        $messageBus->handle($denormalizedMessage)->shouldBeCalled();

        $this->execute($amqpMessage);
    }

    function it_logs_exception_message_if_denormalization_fails(
        DenormalizerInterface $denormalizer,
        LoggerInterface $logger
    ): void {
        $amqpMessage = new AMQPMessage('Invalid message body');

        $denormalizer->denormalize($amqpMessage)->willThrow(new DenormalizationFailedException('Message body is invalid'));

        $logger->error(Argument::containingString('Message body is invalid'))->shouldBeCalled();
        $logger->error(Argument::containingString('Invalid message body'))->shouldBeCalled();

        $this->execute($amqpMessage);
    }

    function it_logs_any_error_that_happened_during_denormalization(
        DenormalizerInterface $denormalizer,
        LoggerInterface $logger
    ): void {
        $amqpMessage = new AMQPMessage('Invalid message body');

        $denormalizer->denormalize($amqpMessage)->will(function () {
            /** @noinspection PhpUndefinedVariableInspection */
            return $undefinedVariable;
        });

        $logger->error(Argument::containingString('notice: Undefined variable: undefinedVariable'))->shouldBeCalled();
        $logger->error(Argument::containingString('Invalid message body'))->shouldBeCalled();

        $this->execute($amqpMessage);
    }

    function it_logs_any_error_that_happened_during_handling_denormalized_message(
        DenormalizerInterface $denormalizer,
        MessageBusInterface $messageBus,
        LoggerInterface $logger
    ): void {
        $amqpMessage = new AMQPMessage('Invalid message body');
        $denormalizedMessage = new \stdClass();

        $denormalizer->denormalize($amqpMessage)->willReturn($denormalizedMessage);

        $messageBus->handle($denormalizedMessage)->will(function () {
            /** @noinspection PhpUndefinedVariableInspection */
            return $undefinedVariable;
        });

        $logger->error(Argument::containingString('notice: Undefined variable: undefinedVariable'))->shouldBeCalled();
        $logger->error(Argument::containingString('Invalid message body'))->shouldBeCalled();

        $this->execute($amqpMessage);
    }
}
