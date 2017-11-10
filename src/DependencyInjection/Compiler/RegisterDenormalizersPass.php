<?php

declare(strict_types=1);

namespace SyliusLabs\RabbitMqSimpleBusBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterDenormalizersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('rabbitmq_simplebus.amqp_denormalizer')) {
            return;
        }

        $denormalizer = $container->findDefinition('rabbitmq_simplebus.amqp_denormalizer');
        foreach ($container->findTaggedServiceIds('rabbitmq_simplebus.amqp_denormalizer') as $id => $attributes) {
            $denormalizer->addMethodCall('addDenormalizer', [new Reference($id)]);
        }
    }
}
