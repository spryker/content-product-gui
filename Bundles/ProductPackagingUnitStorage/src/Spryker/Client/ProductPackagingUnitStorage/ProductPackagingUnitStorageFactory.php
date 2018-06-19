<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductPackagingUnitStorage;

use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ProductPackagingUnitStorage\Dependency\Client\ProductPackagingUnitStorageToStorageClientInterface;
use Spryker\Client\ProductPackagingUnitStorage\Dependency\Service\ProductPackagingUnitStorageToSynchronizationServiceBridge;
use Spryker\Client\ProductPackagingUnitStorage\Storage\ProductPackagingUnitStorageKeyGenerator;
use Spryker\Client\ProductPackagingUnitStorage\Storage\ProductPackagingUnitStorageKeyGeneratorInterface;
use Spryker\Client\ProductPackagingUnitStorage\Storage\ProductPackagingUnitStorageReader;
use Spryker\Client\ProductPackagingUnitStorage\Storage\ProductPackagingUnitStorageReaderInterface;

class ProductPackagingUnitStorageFactory extends AbstractFactory
{
    /**
     * @return \Spryker\Client\ProductPackagingUnitStorage\Storage\ProductPackagingUnitStorageReaderInterface
     */
    public function createPriceAbstractStorageReader(): ProductPackagingUnitStorageReaderInterface
    {
        return new ProductPackagingUnitStorageReader($this->getStorage(), $this->createPriceProductStorageKeyGenerator());
    }

    /**
     * @return \Spryker\Client\ProductPackagingUnitStorage\Dependency\Client\ProductPackagingUnitStorageToStorageClientInterface
     */
    public function getStorage(): ProductPackagingUnitStorageToStorageClientInterface
    {
        return $this->getProvidedDependency(ProductPackagingUnitStorageDependencyProvider::CLIENT_STORAGE);
    }

    /**
     * @return \Spryker\Client\ProductPackagingUnitStorage\Storage\ProductPackagingUnitStorageKeyGeneratorInterface
     */
    public function createPriceProductStorageKeyGenerator(): ProductPackagingUnitStorageKeyGeneratorInterface
    {
        return new ProductPackagingUnitStorageKeyGenerator($this->getSynchronizationService());
    }

    /**
     * @return \Spryker\Client\ProductPackagingUnitStorage\Dependency\Service\ProductPackagingUnitStorageToSynchronizationServiceBridge
     */
    public function getSynchronizationService(): ProductPackagingUnitStorageToSynchronizationServiceBridge
    {
        return $this->getProvidedDependency(ProductPackagingUnitStorageDependencyProvider::SERVICE_SYNCHRONIZATION);
    }
}
