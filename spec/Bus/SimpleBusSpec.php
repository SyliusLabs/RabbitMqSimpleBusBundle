<?php

namespace spec\SyliusLabs\RabbitMqSimpleBusBundle\Bus;

use PhpSpec\ObjectBehavior;
use SimpleBus\Message\Bus\MessageBus;
use SyliusLabs\RabbitMqSimpleBusBundle\Bus\MessageBusInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class SimpleBusSpec extends ObjectBehavior
{
    function let(MessageBus $messageBus)
    {
        $this->beConstructedWith($messageBus);
    }

    function it_is_a_message_bus()
    {
        $this->shouldImplement(MessageBusInterface::class);
    }

    function it_delegates_handling_messages_to_simple_bus(MessageBus $messageBus)
    {
        $message = new \stdClass();

        $messageBus->handle($message)->shouldBeCalled();

        $this->handle($message);
    }
}
