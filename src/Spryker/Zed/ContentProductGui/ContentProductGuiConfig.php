<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ContentProductGui;

use Spryker\Zed\Kernel\AbstractBundleConfig;

/**
 * @method \Spryker\Shared\ContentProductGui\ContentProductGuiConfig getSharedConfig()
 */
class ContentProductGuiConfig extends AbstractBundleConfig
{
    /**
     * @uses \Spryker\Zed\ContentProduct\ContentProductConfig::MAX_NUMBER_PRODUCTS_IN_PRODUCT_ABSTRACT_LIST
     *
     * Should be more than in max count of list abstract products
     *
     * @var int
     */
    public const MAX_NUMBER_PRODUCTS_IN_PRODUCT_ABSTRACT_LIST = 30;

    /**
     * @api
     *
     * @return array<string, string>
     */
    public function getContentWidgetTemplates(): array
    {
        return $this->getSharedConfig()->getContentWidgetTemplates();
    }

    /**
     * @api
     *
     * @return string
     */
    public function getTwigFunctionName(): string
    {
        return $this->getSharedConfig()->getTwigFunctionName();
    }

    /**
     * Specification:
     * - Return the max number of abstract products that can be shown in the list of selected products.
     *
     * @api
     *
     * @return int
     */
    public function getMaxProductsInProductAbstractList(): int
    {
        return static::MAX_NUMBER_PRODUCTS_IN_PRODUCT_ABSTRACT_LIST;
    }
}
