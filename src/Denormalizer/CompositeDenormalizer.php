<?php

declare(strict_types=1);

namespace SyliusLabs\RabbitMqSimpleBusBundle\Denormalizer;

use PhpAmqpLib\Message\AMQPMessage;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class CompositeDenormalizer implements DenormalizerInterface
{
    /**
     * @var DenormalizerInterface[]
     */
    private $denormalizers = [];

    /**
     * @param DenormalizerInterface $denormalizer
     */
    public function addDenormalizer(DenormalizerInterface $denormalizer): void
    {
        $this->denormalizers[] = $denormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(AMQPMessage $message): bool
    {
        foreach ($this->denormalizers as $denormalizer) {
            if ($denormalizer->supports($message)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize(AMQPMessage $message)
    {
        foreach ($this->denormalizers as $denormalizer) {
            if ($denormalizer->supports($message)) {
                return $denormalizer->denormalize($message);
            }
        }

        throw new DenormalizationFailedException('There is no denormalizer supporting this kind of message!');
    }
}
