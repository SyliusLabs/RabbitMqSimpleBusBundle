<?php

declare(strict_types=1);

namespace SyliusLabs\RabbitMqSimpleBusBundle\Denormalizer;

use PhpAmqpLib\Message\AMQPMessage;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface DenormalizerInterface
{
    /**
     * @param AMQPMessage $message
     *
     * @return bool
     */
    public function supports(AMQPMessage $message): bool;

    /**
     * @param AMQPMessage $message
     *
     * @return object
     *
     * @throws DenormalizationFailedException
     */
    public function denormalize(AMQPMessage $message);
}
