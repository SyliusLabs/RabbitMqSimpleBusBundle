<?php

namespace Tests\SyliusLabs\RabbitMqSimpleBusBundle\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use SyliusLabs\RabbitMqSimpleBusBundle\DependencyInjection\Compiler\RegisterDenormalizersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class RegisterDenormalizersPassTest extends AbstractCompilerPassTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterDenormalizersPass());
    }

    /**
     * @test
     */
    public function it_registers_tagged_denormalizers_in_a_composite_one()
    {
        $compositeDenormalizer = new Definition();
        $this->setDefinition('rabbitmq_simplebus.amqp_denormalizer', $compositeDenormalizer);

        $taggedDenormalizer = new Definition();
        $taggedDenormalizer->addTag('rabbitmq_simplebus.amqp_denormalizer');
        $this->setDefinition('acme.amqp_denormalizer', $taggedDenormalizer);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'rabbitmq_simplebus.amqp_denormalizer',
            'addDenormalizer',
            [new Reference('acme.amqp_denormalizer')]
        );
    }
}
