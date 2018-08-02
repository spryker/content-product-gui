<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MinimumOrderValue\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\GlobalMinimumOrderValueTransfer;
use Generated\Shared\Transfer\MinimumOrderValueLocalizedMessageTransfer;
use Generated\Shared\Transfer\MinimumOrderValueTransfer;
use Generated\Shared\Transfer\MinimumOrderValueTypeTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Orm\Zed\MinimumOrderValue\Persistence\SpyMinimumOrderValue;
use Orm\Zed\MinimumOrderValue\Persistence\SpyMinimumOrderValueType;

class MinimumOrderValueMapper implements MinimumOrderValueMapperInterface
{
    /**
     * @param \Orm\Zed\MinimumOrderValue\Persistence\SpyMinimumOrderValueType $spyMinimumOrderValueType
     * @param \Generated\Shared\Transfer\MinimumOrderValueTypeTransfer $minimumOrderValueTypeTransfer
     *
     * @return \Generated\Shared\Transfer\MinimumOrderValueTypeTransfer
     */
    public function mapMinimumOrderValueTypeEntityToTransfer(
        SpyMinimumOrderValueType $spyMinimumOrderValueType,
        MinimumOrderValueTypeTransfer $minimumOrderValueTypeTransfer
    ): MinimumOrderValueTypeTransfer {
        $minimumOrderValueTypeTransfer
            ->fromArray($spyMinimumOrderValueType->toArray(), true)
            ->setIdMinimumOrderValueType($spyMinimumOrderValueType->getIdMinOrderValueType());

        return $minimumOrderValueTypeTransfer;
    }

    /**
     * @param \Orm\Zed\MinimumOrderValue\Persistence\SpyMinimumOrderValue $minimumOrderValueEntity
     * @param \Generated\Shared\Transfer\GlobalMinimumOrderValueTransfer $globalMinimumOrderValueTransfer
     *
     * @return \Generated\Shared\Transfer\GlobalMinimumOrderValueTransfer
     */
    public function mapGlobalMinimumOrderValueEntityToTransfer(
        SpyMinimumOrderValue $minimumOrderValueEntity,
        GlobalMinimumOrderValueTransfer $globalMinimumOrderValueTransfer
    ): GlobalMinimumOrderValueTransfer {
        $globalMinimumOrderValueTransfer->fromArray($minimumOrderValueEntity->toArray(), true)
            ->setMinimumOrderValue(
                (new MinimumOrderValueTransfer())->fromArray($minimumOrderValueEntity->toArray(), true)
            )->setIdMinimumOrderValue($minimumOrderValueEntity->getIdMinOrderValue());

        if (!$globalMinimumOrderValueTransfer->getMinimumOrderValue()->getMinimumOrderValueType()) {
            $globalMinimumOrderValueTransfer->getMinimumOrderValue()->setMinimumOrderValueType(new MinimumOrderValueTypeTransfer());
        }
        $globalMinimumOrderValueTransfer->getMinimumOrderValue()->setMinimumOrderValueType(
            $globalMinimumOrderValueTransfer->getMinimumOrderValue()->getMinimumOrderValueType()->fromArray(
                $minimumOrderValueEntity->getMinimumOrderValueType()->toArray(),
                true
            )
        );

        if (!$globalMinimumOrderValueTransfer->getCurrency()) {
            $globalMinimumOrderValueTransfer->setCurrency(new CurrencyTransfer());
        }
        $globalMinimumOrderValueTransfer->setCurrency(
            $globalMinimumOrderValueTransfer->getCurrency()->fromArray(
                $minimumOrderValueEntity->getCurrency()->toArray(),
                true
            )
        );

        if (!$globalMinimumOrderValueTransfer->getStore()) {
            $globalMinimumOrderValueTransfer->setStore(new StoreTransfer());
        }
        $globalMinimumOrderValueTransfer->setStore(
            $globalMinimumOrderValueTransfer->getStore()->fromArray(
                $minimumOrderValueEntity->getStore()->toArray(),
                true
            )
        );

        foreach ($minimumOrderValueEntity->getSpyMinimumOrderValueLocalizedMessages() as $minimumOrderValueLocalizedMessageEntity) {
            $globalMinimumOrderValueTransfer->getMinimumOrderValue()->addLocalizedMessage(
                (new MinimumOrderValueLocalizedMessageTransfer())->fromArray(
                    $minimumOrderValueLocalizedMessageEntity->toArray(),
                    true
                )->setLocaleCode($minimumOrderValueLocalizedMessageEntity->getLocale()->getLocaleName())
            );
        }

        return $globalMinimumOrderValueTransfer;
    }
}
