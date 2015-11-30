<?php

namespace SprykerEngine\Client\Kernel;

abstract class AbstractDependencyProvider implements DependencyProviderInterface
{
    const CLIENT_ZED_REQUEST = 'zed request client';
    const CLIENT_SESSION = 'session client';
    const CLIENT_KV_STORAGE = 'kv storage client';
    const CLIENT_SEARCH = 'search client';

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideServiceLayerDependencies(Container $container)
    {
        $this->addSessionClient($container);
        $this->addZedClient($container);
        $this->addStorageClient($container);
        $this->addSearchClient($container);

        return $container;
    }

    /**
     * @param Container $container
     */
    protected function addSessionClient(Container $container)
    {
        $container[self::CLIENT_SESSION] = function (Container $container) {
            return $container->getLocator()->session()->client();
        };
    }

    /**
     * @param Container $container
     */
    protected function addZedClient(Container $container)
    {
        $container[self::CLIENT_ZED_REQUEST] = function (Container $container) {
            return $container->getLocator()->zedRequest()->client();
        };
    }

    /**
     * @param Container $container
     */
    protected function addStorageClient(Container $container)
    {
        $container[self::CLIENT_KV_STORAGE] = function (Container $container) {
            return $container->getLocator()->storage()->client();
        };
    }

    /**
     * @param Container $container
     */
    protected function addSearchClient(Container $container)
    {
        $container[self::CLIENT_SEARCH] = function (Container $container) {
            return $container->getLocator()->search()->client();
        };
    }

}
