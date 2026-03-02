<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ContentProductGui\Communication;

use Orm\Zed\Product\Persistence\SpyProductAbstractQuery;
use Spryker\Zed\ContentProductGui\Communication\Form\Constraints\ContentProductAbstractListConstraint;
use Spryker\Zed\ContentProductGui\Communication\Mapper\ContentGui\ContentProductContentGuiEditorConfigurationMapper;
use Spryker\Zed\ContentProductGui\Communication\Mapper\ContentGui\ContentProductContentGuiEditorConfigurationMapperInterface;
use Spryker\Zed\ContentProductGui\Communication\Table\ProductAbstractSelectedTable;
use Spryker\Zed\ContentProductGui\Communication\Table\ProductAbstractViewTable;
use Spryker\Zed\ContentProductGui\ContentProductGuiDependencyProvider;
use Spryker\Zed\ContentProductGui\Dependency\Facade\ContentProductGuiToContentProductInterface;
use Spryker\Zed\ContentProductGui\Dependency\Facade\ContentProductGuiToLocaleInterface;
use Spryker\Zed\ContentProductGui\Dependency\Facade\ContentProductGuiToProductImageInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;

/**
 * @method \Spryker\Zed\ContentProductGui\ContentProductGuiConfig getConfig()
 */
class ContentProductGuiCommunicationFactory extends AbstractCommunicationFactory
{
    public function createProductAbstractViewTable(?string $identifierSuffix = null): ProductAbstractViewTable
    {
        return new ProductAbstractViewTable(
            $this->getProductQueryContainer(),
            $this->getProductImageFacade(),
            $this->getLocaleFacade()->getCurrentLocale(),
            $identifierSuffix,
        );
    }

    public function createProductAbstractSelectedTable(array $idProductAbstracts, ?string $identifierSuffix = null): ProductAbstractSelectedTable
    {
        return new ProductAbstractSelectedTable(
            $this->getProductQueryContainer(),
            $this->getProductImageFacade(),
            $this->getLocaleFacade()->getCurrentLocale(),
            $identifierSuffix,
            $idProductAbstracts,
            $this->getConfig(),
        );
    }

    public function createContentProductAbstractListConstraint(): ContentProductAbstractListConstraint
    {
        return new ContentProductAbstractListConstraint($this->getContentProductFacade());
    }

    public function createContentProductContentGuiEditorMapper(): ContentProductContentGuiEditorConfigurationMapperInterface
    {
        return new ContentProductContentGuiEditorConfigurationMapper($this->getConfig());
    }

    public function getProductImageFacade(): ContentProductGuiToProductImageInterface
    {
        return $this->getProvidedDependency(ContentProductGuiDependencyProvider::FACADE_PRODUCT_IMAGE);
    }

    public function getProductQueryContainer(): SpyProductAbstractQuery
    {
        return $this->getProvidedDependency(ContentProductGuiDependencyProvider::PROPEL_QUERY_PRODUCT_ABSTRACT);
    }

    public function getLocaleFacade(): ContentProductGuiToLocaleInterface
    {
        return $this->getProvidedDependency(ContentProductGuiDependencyProvider::FACADE_LOCALE);
    }

    public function getContentProductFacade(): ContentProductGuiToContentProductInterface
    {
        return $this->getProvidedDependency(ContentProductGuiDependencyProvider::FACADE_CONTENT_PRODUCT);
    }
}
