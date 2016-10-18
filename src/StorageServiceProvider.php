<?php

namespace Devim\Provider\StorageServiceProvider;

use Devim\Provider\StorageServiceProvider\EventSubscriber\ConsoleEventSubscriber;
use Devim\Provider\StorageServiceProvider\Exception\StorageException;
use Devim\Provider\StorageServiceProvider\Storage\FileStorage;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class StorageServiceProvider.
 */
class StorageServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface, BootableProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $container A container instance
     */
    public function register(Container $container)
    {
        $container['storage.upload_endpoint'] = '/file/upload';
        $container['storage.directory'] = '';
        $container['storage.public_url_template'] = '';

        $container['storage'] = function () use ($container) {
            return new FileStorage($container['storage.directory'], $container['storage.public_url_template']);
        };
    }

    /**
     * @param Container $app
     * @param EventDispatcherInterface $dispatcher
     */
    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber(new ConsoleEventSubscriber());
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     *
     * @param Application $app
     *
     * @throws StorageException
     */
    public function boot(Application $app)
    {
        if (!@mkdir($app['storage.directory'], 0777, true) && !is_dir($app['storage.directory'])) {
            throw new StorageException(sprintf('Directory "%s" is not create', $app['storage.directory']));
        }

        if (!is_writable($app['storage.directory'])) {
            throw new StorageException(sprintf('Directory "%s" is not writable', $app['storage.directory']));
        }
    }
}
