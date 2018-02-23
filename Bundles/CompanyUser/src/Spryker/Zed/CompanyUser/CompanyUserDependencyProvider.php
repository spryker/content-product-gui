<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyUser;

use Spryker\Zed\CompanyUser\Dependency\Facade\CompanyUserToCustomerFacadeBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

class CompanyUserDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_CUSTOMER = 'FACADE_CUSTOMER';

    public const PLUGINS_CUSTOMER_SAVE = 'PLUGINS_CUSTOMER_SAVE';
    public const PLUGINS_CUSTOMER_HYDRATE = 'PLUGINS_CUSTOMER_HYDRATE';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addCustomerFacade($container);
        $container = $this->addCustomerSavePlugins($container);
        $container = $this->addUserSaveHydrationPlugins($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCustomerFacade(Container $container): Container
    {
        $container[static::FACADE_CUSTOMER] = function (Container $container) {
            return new CompanyUserToCustomerFacadeBridge($container->getLocator()->customer()->facade());
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCustomerSavePlugins(Container $container): Container
    {
        $container[static::PLUGINS_CUSTOMER_SAVE] = function () {
            return $this->getCompanyUserSavePlugins();
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUserSaveHydrationPlugins(Container $container): Container
    {
        $container[static::PLUGINS_CUSTOMER_HYDRATE] = function () {
            return $this->getCompanyUserHydrationPlugins();
        };

        return $container;
    }

    /**
     * @return \Spryker\Zed\CompanyUser\Dependency\Plugin\CompanyUserSavePluginInterface[]
     */
    protected function getCompanyUserSavePlugins(): array
    {
        return [];
    }

    /**
     * @return \Spryker\Zed\CompanyUser\Dependency\Plugin\CompanyUserHydrationPluginInterface[]
     */
    protected function getCompanyUserHydrationPlugins(): array
    {
        return [];
    }
}