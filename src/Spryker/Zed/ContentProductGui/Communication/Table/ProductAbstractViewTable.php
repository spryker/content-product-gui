<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ContentProductGui\Communication\Table;

use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class ProductAbstractViewTable extends AbstractProductAbstractTable
{
    /**
     * @var string
     */
    public const TABLE_IDENTIFIER = 'product-abstract-view-table';

    /**
     * @var string
     */
    public const TABLE_CLASS = 'product-abstract-view-table gui-table-data';

    /**
     * @var string
     */
    public const BASE_URL = '/content-product-gui/product-abstract/';

    /**
     * @var string
     */
    public const COL_SELECTED = 'Selected';

    /**
     * @var string
     */
    public const COL_ALIAS_NAME = 'name';

    protected const string SELECTOR_WRAPPER = '.id-product-abstract-fields';

    protected const string SELECTOR_INPUTS_WRAPPER = '.js-selected-products-wrapper';

    protected const string SELECTOR_ADD_BUTTON = '.js-add-product-abstract';

    protected const string SELECTOR_REMOVE_BUTTON = '.js-delete-product-abstract';

    protected const string SELECTOR_REORDER_BUTTON = '.js-reorder-product-abstract';

    protected const string CLASS_CLEAR_ALL_BUTTON = 'clear-fields';

    protected function configure(TableConfiguration $config): TableConfiguration
    {
        $this->baseUrl = static::BASE_URL;
        $this->defaultUrl = static::TABLE_IDENTIFIER;
        $this->tableClass = static::TABLE_CLASS;

        $identifierSuffix = !$this->identifierSuffix ?
            static::TABLE_IDENTIFIER :
            sprintf('%s-%s', static::TABLE_IDENTIFIER, $this->identifierSuffix);
        $this->setTableIdentifier($identifierSuffix);

        $config->setHeader([
            static::COL_ID_PRODUCT_ABSTRACT => static::HEADER_ID_PRODUCT_ABSTRACT,
            static::COL_SKU => static::HEADER_SKU,
            static::COL_IMAGE => static::COL_IMAGE,
            static::COL_NAME => static::HEADER_NAME,
            static::COL_STORES => static::COL_STORES,
            static::COL_STATUS => static::COL_STATUS,
            static::COL_SELECTED => static::COL_SELECTED,
        ]);

        $config->setSearchable([
            static::COL_ID_PRODUCT_ABSTRACT,
            static::COL_SKU,
            static::COL_NAME,
        ]);

        $config->setRawColumns([
            static::COL_IMAGE,
            static::COL_STORES,
            static::COL_STATUS,
            static::COL_SELECTED,
        ]);

        $config->setStateSave(false);

        // Assigning a product moves it into the selected table and into the form collection of the wrapper.
        $config->setTableAttributes([
            'data-assignable' => [
                'selectedTableSelector' => '#' . $this->getSelectedTableIdentifier(),
                'colId' => static::COL_ID_PRODUCT_ABSTRACT,
                'wrapperSelector' => static::SELECTOR_WRAPPER,
                'inputsWrapperSelector' => static::SELECTOR_INPUTS_WRAPPER,
                'addButtonSelector' => static::SELECTOR_ADD_BUTTON,
                'removeButtonSelector' => static::SELECTOR_REMOVE_BUTTON,
                'reorderButtonSelector' => static::SELECTOR_REORDER_BUTTON,
                'clearAllButtonClass' => static::CLASS_CLEAR_ALL_BUTTON,
            ],
        ]);

        return $config;
    }

    protected function getSelectedTableIdentifier(): string
    {
        if (!$this->identifierSuffix) {
            return ProductAbstractSelectedTable::TABLE_IDENTIFIER;
        }

        return sprintf('%s-%s', ProductAbstractSelectedTable::TABLE_IDENTIFIER, $this->identifierSuffix);
    }

    /**
     * @module Product
     *
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config): array
    {
        $query = $this->productQueryContainer
            ->useSpyProductAbstractLocalizedAttributesQuery()
                ->filterByFkLocale($this->localeTransfer->getIdLocale())
            ->endUse();
        $queryResults = $this->runQuery($query, $config, true);

        $results = [];
        foreach ($queryResults as $productAbstractEntity) {
            $results[] = $this->formatRow($productAbstractEntity);
        }

        return $results;
    }

    protected function formatRow(SpyProductAbstract $productAbstractEntity): array
    {
        return [
            static::COL_ID_PRODUCT_ABSTRACT => $productAbstractEntity->getIdProductAbstract(),
            static::COL_SKU => $productAbstractEntity->getSku(),
            static::COL_IMAGE => $this->getProductPreview($this->getProductPreviewUrl($productAbstractEntity)),
            static::COL_NAME => $productAbstractEntity->getSpyProductAbstractLocalizedAttributess()->getFirst()->getName(),
            static::COL_STORES => $this->getStoreNames($productAbstractEntity->getSpyProductAbstractStores()->getArrayCopy()),
            static::COL_STATUS => $this->getStatusLabel($this->getAbstractProductStatus($productAbstractEntity)),
            static::COL_SELECTED => $this->getAddButtonField($productAbstractEntity->getIdProductAbstract()),
        ];
    }

    protected function getAddButtonField(int $idProductAbstract): string
    {
        return $this->generateButton(
            '#',
            'Add to list',
            [
                'class' => 'btn-create js-add-product-abstract',
                'data-id' => $idProductAbstract,
                'icon' => 'fa-plus',
                'onclick' => 'return false;',
            ],
        );
    }
}
