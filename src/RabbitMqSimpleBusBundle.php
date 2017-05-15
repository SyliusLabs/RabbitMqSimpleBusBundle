<?php

namespace SyliusLabs\RabbitMqSimpleBusBundle;

use SyliusLabs\RabbitMqSimpleBusBundle\DependencyInjection\Compiler\RegisterDenormalizersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class RabbitMqSimpleBusBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterDenormalizersPass());
    }
}
