<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductPackagingUnit\Business\Model\ProductPackagingUnit;

use Generated\Shared\Transfer\ProductMeasurementSalesUnitTransfer;
use Generated\Shared\Transfer\ProductPackagingLeadProductTransfer;
use Generated\Shared\Transfer\ProductPackagingUnitAmountTransfer;
use Generated\Shared\Transfer\ProductPackagingUnitTransfer;
use Spryker\Zed\ProductPackagingUnit\Dependency\Facade\ProductPackagingUnitToProductMeasurementUnitFacadeInterface;
use Spryker\Zed\ProductPackagingUnit\Persistence\ProductPackagingUnitRepositoryInterface;

class ProductPackagingUnitReader implements ProductPackagingUnitReaderInterface
{
    /**
     * default values for packaging unit storage values.
     */
    protected const PRODUCT_ABSTRACT_STORAGE_DEFAULT_VALUES = [
        ProductPackagingUnitAmountTransfer::DEFAULT_AMOUNT => 1,
        ProductPackagingUnitAmountTransfer::IS_VARIABLE => false,
        ProductPackagingUnitAmountTransfer::AMOUNT_MIN => 1,
        ProductPackagingUnitAmountTransfer::AMOUNT_INTERVAL => 1,
    ];

    /**
     * @var \Spryker\Zed\ProductPackagingUnit\Persistence\ProductPackagingUnitRepositoryInterface
     */
    protected $repository;

    /**
     * @var \Spryker\Zed\ProductPackagingUnit\Dependency\Facade\ProductPackagingUnitToProductMeasurementUnitFacadeInterface
     */
    protected $productMeasurementUnitFacade;

    /**
     * @param \Spryker\Zed\ProductPackagingUnit\Persistence\ProductPackagingUnitRepositoryInterface $repository
     * @param \Spryker\Zed\ProductPackagingUnit\Dependency\Facade\ProductPackagingUnitToProductMeasurementUnitFacadeInterface $productMeasurementUnitFacade
     */
    public function __construct(
        ProductPackagingUnitRepositoryInterface $repository,
        ProductPackagingUnitToProductMeasurementUnitFacadeInterface $productMeasurementUnitFacade
    ) {
        $this->repository = $repository;
        $this->productMeasurementUnitFacade = $productMeasurementUnitFacade;
    }

    /**
     * @param int $productPackagingUnitId
     *
     * @return \Generated\Shared\Transfer\ProductPackagingUnitTransfer|null
     */
    public function getProductPackagingUnitById(
        int $productPackagingUnitId
    ): ?ProductPackagingUnitTransfer {
        $productPackagingUnitTransfer = $this->repository->getProductPackagingUnitById($productPackagingUnitId);

        if ($productPackagingUnitTransfer && !$productPackagingUnitTransfer->getProductPackagingUnitAmount()) {
            $this->hydrateWithDefaultAmount($productPackagingUnitTransfer);
        }

        return $productPackagingUnitTransfer;
    }

    /**
     * @param string $productPackagingUnitSku
     *
     * @return \Generated\Shared\Transfer\ProductPackagingUnitTransfer|null
     */
    public function getProductPackagingUnitBySku(
        string $productPackagingUnitSku
    ): ?ProductPackagingUnitTransfer {
        $productPackagingUnitTransfer = $this->repository->getProductPackagingUnitBySku($productPackagingUnitSku);

        if ($productPackagingUnitTransfer && !$productPackagingUnitTransfer->getProductPackagingUnitAmount()) {
            $this->hydrateWithDefaultAmount($productPackagingUnitTransfer);
        }

        return $productPackagingUnitTransfer;
    }

    /**
     * @param string $productPackagingUnitSku
     *
     * @return \Generated\Shared\Transfer\ProductPackagingLeadProductTransfer|null
     */
    public function getProductPackagingLeadProductByProductPackagingSku(string $productPackagingUnitSku): ?ProductPackagingLeadProductTransfer
    {
        return $this->repository->getProductPackagingLeadProductByProductPackagingSku($productPackagingUnitSku);
    }

    /**
     * @param \Generated\Shared\Transfer\ProductPackagingUnitTransfer $productPackagingUnitTransfer
     *
     * @return \Generated\Shared\Transfer\ProductPackagingUnitTransfer
     */
    protected function hydrateWithDefaultAmount(ProductPackagingUnitTransfer $productPackagingUnitTransfer): ProductPackagingUnitTransfer
    {
        $productPackagingUnitTransfer->setProductPackagingUnitAmount(
            $this->createDefaultProductPackagingUnitAmountTransfer()
        );

        return $productPackagingUnitTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\ProductPackagingUnitAmountTransfer
     */
    protected function createDefaultProductPackagingUnitAmountTransfer(): ProductPackagingUnitAmountTransfer
    {
        return (new ProductPackagingUnitAmountTransfer())
            ->fromArray(
                static::PRODUCT_ABSTRACT_STORAGE_DEFAULT_VALUES
            );
    }

    /**
     * @param int $idProductMeasurementSalesUnit
     *
     * @return \Generated\Shared\Transfer\ProductMeasurementSalesUnitTransfer
     */
    public function getProductMeasurementSalesUnitTransfer(int $idProductMeasurementSalesUnit): ProductMeasurementSalesUnitTransfer
    {
        return $this->productMeasurementUnitFacade->getProductMeasurementSalesUnitTransfer($idProductMeasurementSalesUnit);
    }
}
