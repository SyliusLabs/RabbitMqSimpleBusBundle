RabbitMqSimpleBusBundle
=======================

Transforms AMQP messages received from RabbitMQ to event handled by SimpleBus.
 
Installation
------------

1. Require this package:

```bash
$ composer require sylius-labs/rabbitmq-simplebus-bundle
```

2. Add bundle to `AppKernel.php`:

```php
public function registerBundles()
{
    $bundles = [
        new \SyliusLabs\RabbitMqSimpleBusBundle\RabbitMqSimpleBusBundle(),
    ];

    return array_merge(parent::registerBundles(), $bundles);
}
```

Usage
-----

1. Create your custom AMQP messages denormalizer:

```php
// src/Acme/CustomDenormalizer.php

namespace Acme;

use PhpAmqpLib\Message\AMQPMessage;
use SyliusLabs\RabbitMqSimpleBusBundle\Denormalizer\DenormalizationFailedException;
use SyliusLabs\RabbitMqSimpleBusBundle\Denormalizer\DenormalizerInterface;

class CustomDenormalizer implements DenormalizerInterface
{
    public function supports(AMQPMessage $message)
    {
        return null !== json_decode($message->getBody(), true);
    }

    public function denormalize(AMQPMessage $message)
    {
        if (!$this->supports($message)) {
            throw new DenormalizationFailedException('Unsupported message!');
        }

        return new CustomEvent(json_decode($message->getBody(), true));
    }
}
```

2. Tag your denormalizer service with `rabbitmq_simplebus.amqp_denormalizer`:

```xml
<!-- app/config/services.xml -->

<?xml version="1.0" encoding="UTF-8"?>
<container xmlns="http://symfony.com/schema/dic/services" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
    <services>
        <service id="acme.custom_denormalizer" class="Acme\CustomDenormalizer">
            <tag name="rabbitmq_simplebus.amqp_denormalizer" />
        </service>
    </services>
</container>
```

```yaml
# app/config/services.yml

services:
    acme.custom_denormalizer:
        class: Acme\CustomDenormalizer
        tags:
            - { name: rabbitmq_simplebus.amqp_denormalizer }
```

3. [Configure RabbitMQ consumer](https://github.com/php-amqplib/RabbitMqBundle#usage):

```yaml
# app/config/config.yml

old_sound_rabbit_mq:
    connections:
        default:
            host: 'localhost'
            port: 5672
            user: 'guest'
            password: 'guest'
    consumers:
        rabbitmq_simplebus:
            connection: default
            exchange_options: { name: 'rabbitmq-simplebus', type: direct }
            queue_options: { name: 'rabbitmq-simplebus' }
            callback: rabbitmq_simplebus.consumer
```

4. Run RabbitMQ consumer:

```bash
$ bin/console rabbitmq:consumer rabbitmq_simplebus
```
