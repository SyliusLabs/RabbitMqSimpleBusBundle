<?php

namespace spec\SyliusLabs\RabbitMqSimpleBusBundle\Denormalizer;

use PhpAmqpLib\Message\AMQPMessage;
use PhpSpec\ObjectBehavior;
use SyliusLabs\RabbitMqSimpleBusBundle\Denormalizer\DenormalizationFailedException;
use SyliusLabs\RabbitMqSimpleBusBundle\Denormalizer\DenormalizerInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class CompositeDenormalizerSpec extends ObjectBehavior
{
    function it_is_a_denormalizer()
    {
        $this->shouldImplement(DenormalizerInterface::class);
    }

    function it_does_not_support_a_message_if_there_are_no_denormalizers()
    {
        $amqpMessage = new AMQPMessage('Message body');

        $this->supports($amqpMessage)->shouldReturn(false);
        $this->shouldThrow(DenormalizationFailedException::class)->during('denormalize', [$amqpMessage]);
    }

    function it_supports_a_message_if_at_least_one_of_denormalizers_support_it(
        DenormalizerInterface $firstDenormalizer,
        DenormalizerInterface $secondDenormalizer
    ) {
        $amqpMessage = new AMQPMessage('Message body');
        $denormalizedMessage = new \stdClass();

        $firstDenormalizer->supports($amqpMessage)->willReturn(false);
        $firstDenormalizer->denormalize($amqpMessage)->shouldNotBeCalled();
        $secondDenormalizer->supports($amqpMessage)->willReturn(true);
        $secondDenormalizer->denormalize($amqpMessage)->willReturn($denormalizedMessage);

        $this->addDenormalizer($firstDenormalizer);
        $this->addDenormalizer($secondDenormalizer);

        $this->supports($amqpMessage)->shouldReturn(true);
        $this->denormalize($amqpMessage)->shouldReturn($denormalizedMessage);
    }

    function it_does_not_support_a_message_if_none_of_the_denormalizers_support_it(
        DenormalizerInterface $firstDenormalizer,
        DenormalizerInterface $secondDenormalizer
    ) {
        $amqpMessage = new AMQPMessage('Message body');

        $firstDenormalizer->supports($amqpMessage)->willReturn(false);
        $firstDenormalizer->denormalize($amqpMessage)->shouldNotBeCalled();
        $secondDenormalizer->supports($amqpMessage)->willReturn(false);
        $secondDenormalizer->denormalize($amqpMessage)->shouldNotBeCalled();;

        $this->addDenormalizer($firstDenormalizer);
        $this->addDenormalizer($secondDenormalizer);

        $this->supports($amqpMessage)->shouldReturn(false);
        $this->shouldThrow(DenormalizationFailedException::class)->during('denormalize', [$amqpMessage]);
    }
}
