<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Product\Business\Product;

use Generated\Shared\Transfer\ProductAbstractTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\UrlTransfer;
use Orm\Zed\Product\Persistence\SpyProductAbstractLocalizedAttributes;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Product\Business\Exception\ProductAbstractAttributesExistException;
use Spryker\Zed\Product\Business\Exception\ProductAbstractExistsException;
use Spryker\Zed\Product\Business\Exception\ProductConcreteAttributesExistException;
use Spryker\Zed\Product\Business\Exception\ProductConcreteExistsException;
use Spryker\Zed\Product\Business\Exception\MissingProductException;
use Spryker\Zed\Product\Dependency\Facade\ProductToTouchInterface;
use Spryker\Zed\Product\Dependency\Facade\ProductToUrlInterface;
use Spryker\Zed\Product\Dependency\Facade\ProductToLocaleInterface;
use Spryker\Zed\Product\Persistence\ProductQueryContainerInterface;
use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Orm\Zed\Product\Persistence\SpyProductLocalizedAttributes;
use Orm\Zed\Product\Persistence\SpyProduct;
use Spryker\Zed\Url\Business\Exception\UrlExistsException;
use Generated\Shared\Transfer\TaxSetTransfer;
use Generated\Shared\Transfer\TaxRateTransfer;

class ProductManager implements ProductManagerInterface
{

    const COL_ID_PRODUCT_CONCRETE = 'SpyProduct.IdProduct';

    const COL_ABSTRACT_SKU = 'SpyProductAbstract.Sku';

    const COL_ID_PRODUCT_ABSTRACT = 'SpyProductAbstract.IdProductAbstract';

    const COL_NAME = 'SpyProductLocalizedAttributes.Name';

    /**
     * @var ProductQueryContainerInterface
     */
    protected $productQueryContainer;

    /**
     * @var ProductToTouchInterface
     */
    protected $touchFacade;

    /**
     * @var ProductToUrlInterface
     */
    protected $urlFacade;

    /**
     * @var ProductToLocaleInterface
     */
    protected $localeFacade;

    /**
     * @var SpyProductAbstract[]
     */
    protected $productAbstractCollectionBySkuCache = [];

    /**
     * @var SpyProduct[]
     */
    protected $productConcreteCollectionBySkuCache = [];

    /**
     * @var array
     */
    protected $productAbstractsBySkuCache;

    /**
     * @param ProductQueryContainerInterface $productQueryContainer
     * @param ProductToTouchInterface $touchFacade
     * @param ProductToUrlInterface $urlFacade
     * @param ProductToLocaleInterface $localeFacade
     */
    public function __construct(
        ProductQueryContainerInterface $productQueryContainer,
        ProductToTouchInterface $touchFacade,
        ProductToUrlInterface $urlFacade,
        ProductToLocaleInterface $localeFacade
    ) {
        $this->productQueryContainer = $productQueryContainer;
        $this->touchFacade = $touchFacade;
        $this->urlFacade = $urlFacade;
        $this->localeFacade = $localeFacade;
    }

    /**
     * @param string $sku
     *
     * @return bool
     */
    public function hasProductAbstract($sku)
    {
        $productAbstractQuery = $this->productQueryContainer->queryProductAbstractBySku($sku);

        return $productAbstractQuery->count() > 0;
    }

    /**
     * @param ProductAbstractTransfer $productAbstractTransfer
     *
     * @throws ProductAbstractExistsException
     * @throws PropelException
     *
     * @return int
     */
    public function createProductAbstract(ProductAbstractTransfer $productAbstractTransfer)
    {
        $sku = $productAbstractTransfer->getSku();

        $encodedAttributes = $this->encodeAttributes($productAbstractTransfer->getAttributes());

        $productAbstract = new SpyProductAbstract();
        $productAbstract
            ->setAttributes($encodedAttributes)
            ->setSku($sku);

        $productAbstract->save();

        $idProductAbstract = $productAbstract->getPrimaryKey();
        $productAbstractTransfer->setIdProductAbstract($idProductAbstract);
        $this->createProductAbstractAttributes($productAbstractTransfer);

        return $idProductAbstract;
    }

    /**
     * @param string $sku
     *
     * @throws MissingProductException
     *
     * @return int
     */
    public function getProductAbstractIdBySku($sku)
    {
        if (!isset($this->productAbstractsBySkuCache[$sku])) {
            $productAbstract = $this->productQueryContainer->queryProductAbstractBySku($sku)->findOne();

            if (!$productAbstract) {
                throw new MissingProductException(
                    sprintf(
                        'Tried to retrieve an product abstract with sku %s, but it does not exist.',
                        $sku
                    )
                );
            }

            $this->productAbstractsBySkuCache[$sku] = $productAbstract;
        }

        return $this->productAbstractsBySkuCache[$sku]->getPrimaryKey();
    }

    /**
     * @param string $sku
     *
     * @throws ProductAbstractExistsException
     *
     * @return void
     */
    protected function checkProductAbstractDoesNotExist($sku)
    {
        if ($this->hasProductAbstract($sku)) {
            throw new ProductAbstractExistsException(
                sprintf(
                    'Tried to create an product abstract with sku %s that already exists',
                    $sku
                )
            );
        }
    }

    /**
     * @param ProductAbstractTransfer $productAbstractTransfer
     *
     * @throws ProductAbstractAttributesExistException
     * @throws PropelException
     *
     * @return void
     */
    protected function createProductAbstractAttributes(ProductAbstractTransfer $productAbstractTransfer)
    {
        $idProductAbstract = $productAbstractTransfer->getIdProductAbstract();

        foreach ($productAbstractTransfer->getLocalizedAttributes() as $localizedAttributes) {
            $locale = $localizedAttributes->getLocale();
            if ($this->hasProductAbstractAttributes($idProductAbstract, $locale)) {
                continue;
            }
            $encodedAttributes = $this->encodeAttributes($localizedAttributes->getAttributes());

            $productAbstractAttributesEntity = new SpyProductAbstractLocalizedAttributes();
            $productAbstractAttributesEntity
                ->setFkProductAbstract($idProductAbstract)
                ->setFkLocale($locale->getIdLocale())
                ->setName($localizedAttributes->getName())
                ->setAttributes($encodedAttributes);

            $productAbstractAttributesEntity->save();
        }
    }

    /**
     * @param int $idProductAbstract
     * @param LocaleTransfer $locale
     *
     * @deprecated Use hasProductAbstractAttributes() instead.
     *
     * @throws ProductAbstractAttributesExistException
     *
     * @return void
     */
    protected function checkProductAbstractAttributesDoNotExist($idProductAbstract, $locale)
    {
        if ($this->hasProductAbstractAttributes($idProductAbstract, $locale)) {
            throw new ProductAbstractAttributesExistException(
                sprintf(
                    'Tried to create abstract attributes for product abstract %s, locale id %s, but it already exists',
                    $idProductAbstract,
                    $locale->getIdLocale()
                )
            );
        }
    }

    /**
     * @param int $idProductAbstract
     * @param LocaleTransfer $locale
     *
     * @return bool
     */
    protected function hasProductAbstractAttributes($idProductAbstract, LocaleTransfer $locale)
    {
        $query = $this->productQueryContainer->queryProductAbstractAttributeCollection(
            $idProductAbstract,
            $locale->getIdLocale()
        );

        return $query->count() > 0;
    }

    /**
     * @param ProductConcreteTransfer $productConcreteTransfer
     * @param int $idProductAbstract
     *
     * @throws ProductConcreteExistsException
     * @throws PropelException
     *
     * @return int
     */
    public function createProductConcrete(ProductConcreteTransfer $productConcreteTransfer, $idProductAbstract)
    {
        $sku = $productConcreteTransfer->getSku();

        $this->checkProductConcreteDoesNotExist($sku);
        $encodedAttributes = $this->encodeAttributes($productConcreteTransfer->getAttributes());

        $productConcreteEntity = new SpyProduct();
        $productConcreteEntity
            ->setSku($sku)
            ->setFkProductAbstract($idProductAbstract)
            ->setAttributes($encodedAttributes)
            ->setIsActive($productConcreteTransfer->getIsActive());

        $productConcreteEntity->save();

        $idProductConcrete = $productConcreteEntity->getPrimaryKey();
        $productConcreteTransfer->setIdProductConcrete($idProductConcrete);
        $this->createProductConcreteAttributes($productConcreteTransfer);

        return $idProductConcrete;
    }

    /**
     * @param string $sku
     *
     * @throws ProductConcreteExistsException
     *
     * @return void
     */
    protected function checkProductConcreteDoesNotExist($sku)
    {
        if ($this->hasProductConcrete($sku)) {
            throw new ProductConcreteExistsException(
                sprintf(
                    'Tried to create a product concrete with sku %s, but it already exists',
                    $sku
                )
            );
        }
    }

    /**
     * @param string $sku
     *
     * @return bool
     */
    public function hasProductConcrete($sku)
    {
        return $this->productQueryContainer->queryProductConcreteBySku($sku)->count() > 0;
    }

    /**
     * @param string $sku
     *
     * @throws MissingProductException
     *
     * @return int
     */
    public function getProductConcreteIdBySku($sku)
    {
        if (!isset($this->productConcreteCollectionBySkuCache[$sku])) {
            $productConcrete = $this->productQueryContainer->queryProductConcreteBySku($sku)->findOne();

            if (!$productConcrete) {
                throw new MissingProductException(
                    sprintf(
                        'Tried to retrieve a product concrete with sku %s, but it does not exist',
                        $sku
                    )
                );
            }

            $this->productConcreteCollectionBySkuCache[$sku] = $productConcrete;
        }

        return $this->productConcreteCollectionBySkuCache[$sku]->getPrimaryKey();
    }

    /**
     * @param ProductConcreteTransfer $productConcreteTransfer
     *
     * @throws ProductConcreteAttributesExistException
     * @throws PropelException
     *
     * @return void
     */
    protected function createProductConcreteAttributes(ProductConcreteTransfer $productConcreteTransfer)
    {
        $idProductConcrete = $productConcreteTransfer->getIdProductConcrete();

        foreach ($productConcreteTransfer->getLocalizedAttributes() as $localizedAttributes) {
            $locale = $localizedAttributes->getLocale();
            $this->checkProductConcreteAttributesDoNotExist($idProductConcrete, $locale);
            $encodedAttributes = $this->encodeAttributes($localizedAttributes->getAttributes());

            $productAttributeEntity = new SpyProductLocalizedAttributes();
            $productAttributeEntity
                ->setFkProduct($idProductConcrete)
                ->setFkLocale($locale->getIdLocale())
                ->setName($localizedAttributes->getName())
                ->setAttributes($encodedAttributes);

            $productAttributeEntity->save();
        }
    }

    /**
     * @param int $idProductConcrete
     * @param LocaleTransfer $locale
     *
     * @throws ProductConcreteAttributesExistException
     *
     * @return void
     */
    protected function checkProductConcreteAttributesDoNotExist($idProductConcrete, LocaleTransfer $locale)
    {
        if ($this->hasProductConcreteAttributes($idProductConcrete, $locale)) {
            throw new ProductConcreteAttributesExistException(
                sprintf(
                    'Tried to create product concrete attributes for product id %s, locale id %s, but they exist',
                    $idProductConcrete,
                    $locale->getIdLocale()
                )
            );
        }
    }

    /**
     * @param int $idProductConcrete
     * @param LocaleTransfer $locale
     *
     * @return bool
     */
    protected function hasProductConcreteAttributes($idProductConcrete, LocaleTransfer $locale)
    {
        $query = $this->productQueryContainer->queryProductConcreteAttributeCollection(
            $idProductConcrete,
            $locale->getIdLocale()
        );

        return $query->count() > 0;
    }

    /**
     * @param int $idProductAbstract
     *
     * @return void
     */
    public function touchProductActive($idProductAbstract)
    {
        $this->touchFacade->touchActive('product_abstract', $idProductAbstract);
    }

    /**
     * @param string $sku
     * @param string $url
     * @param LocaleTransfer $locale
     *
     * @throws PropelException
     * @throws UrlExistsException
     * @throws MissingProductException
     *
     * @return UrlTransfer
     */
    public function createProductUrl($sku, $url, LocaleTransfer $locale)
    {
        $idProductAbstract = $this->getProductAbstractIdBySku($sku);

        return $this->createProductUrlByIdProduct($idProductAbstract, $url, $locale);
    }

    /**
     * @param int $idProductAbstract
     * @param string $url
     * @param LocaleTransfer $locale
     *
     * @throws PropelException
     * @throws UrlExistsException
     * @throws MissingProductException
     *
     * @return UrlTransfer
     */
    public function createProductUrlByIdProduct($idProductAbstract, $url, LocaleTransfer $locale)
    {
        return $this->urlFacade->createUrl($url, $locale, 'product_abstract', $idProductAbstract);
    }

    /**
     * @param string $sku
     * @param string $url
     * @param LocaleTransfer $locale
     *
     * @throws PropelException
     * @throws UrlExistsException
     * @throws MissingProductException
     *
     * @return UrlTransfer
     */
    public function createAndTouchProductUrl($sku, $url, LocaleTransfer $locale)
    {
        $url = $this->createProductUrl($sku, $url, $locale);
        $this->urlFacade->touchUrlActive($url->getIdUrl());

        return $url;
    }

    /**
     * @param int $idProductAbstract
     * @param string $url
     * @param LocaleTransfer $locale
     *
     * @throws PropelException
     * @throws UrlExistsException
     * @throws MissingProductException
     *
     * @return UrlTransfer
     */
    public function createAndTouchProductUrlByIdProduct($idProductAbstract, $url, LocaleTransfer $locale)
    {
        $url = $this->createProductUrlByIdProduct($idProductAbstract, $url, $locale);
        $this->urlFacade->touchUrlActive($url->getIdUrl());

        return $url;
    }

    /**
     * @param string $sku
     *
     * @throws MissingProductException
     *
     * @return float
     */
    public function getEffectiveTaxRateForProductConcrete($sku)
    {
        $productConcrete = $this->productQueryContainer->queryProductConcreteBySku($sku)->findOne();

        if (!$productConcrete) {
            throw new MissingProductException(
                sprintf(
                    'Tried to retrieve a product concrete with sku %s, but it does not exist.',
                    $sku
                )
            );
        }

        $productAbstract = $productConcrete->getSpyProductAbstract();

        $effectiveTaxRate = 0;

        $taxSetEntity = $productAbstract->getSpyTaxSet();
        if ($taxSetEntity === null) {
            return $effectiveTaxRate;
        }

        foreach ($taxSetEntity->getSpyTaxRates() as $taxRateEntity) {
            $effectiveTaxRate += $taxRateEntity->getRate();
        }

        return $effectiveTaxRate;
    }

    /**
     * @param string $concreteSku
     *
     * @throws MissingProductException
     *
     * @return ProductConcreteTransfer
     */
    public function getProductConcrete($concreteSku)
    {
        $localeTransfer = $this->localeFacade->getCurrentLocale();

        $productConcreteQuery = $this->productQueryContainer->queryProductWithAttributesAndProductAbstract(
            $concreteSku, $localeTransfer->getIdLocale()
        );

        $productConcreteQuery->select([
            self::COL_ID_PRODUCT_CONCRETE,
            self::COL_ABSTRACT_SKU,
            self::COL_ID_PRODUCT_ABSTRACT,
            self::COL_NAME,
        ]);

        $productConcrete = $productConcreteQuery->findOne();

        if (!$productConcrete) {
            throw new MissingProductException(
                sprintf(
                    'Tried to retrieve a product concrete with sku %s, but it does not exist.',
                    $concreteSku
                )
            );
        }

        $productConcreteTransfer = new ProductConcreteTransfer();
        $productConcreteTransfer->setSku($concreteSku)
            ->setIdProductConcrete($productConcrete[self::COL_ID_PRODUCT_CONCRETE])
            ->setProductAbstractSku($productConcrete[self::COL_ABSTRACT_SKU])
            ->setIdProductAbstract($productConcrete[self::COL_ID_PRODUCT_ABSTRACT])
            ->setName($productConcrete[self::COL_NAME]);

        $this->addTaxesToProductTransfer($productConcreteTransfer);

        return $productConcreteTransfer;
    }

    /**
     * @param ProductConcreteTransfer $productConcreteTransfer
     *
     * @return void
     */
    private function addTaxesToProductTransfer(ProductConcreteTransfer $productConcreteTransfer)
    {
        $taxSetEntity = $this->productQueryContainer
            ->queryTaxSetForProductAbstract($productConcreteTransfer->getIdProductAbstract())
            ->findOne();

        if ($taxSetEntity === null) {
            return;
        }

        $taxTransfer = new TaxSetTransfer();
        $taxTransfer->setIdTaxSet($taxSetEntity->getIdTaxSet())
            ->setName($taxSetEntity->getName());

        foreach ($taxSetEntity->getSpyTaxRates() as $taxRate) {
            $taxRateTransfer = new TaxRateTransfer();
            $taxRateTransfer->setIdTaxRate($taxRate->getIdTaxRate())
                ->setName($taxRate->getName())
                ->setRate($taxRate->getRate());

            $taxTransfer->addTaxRate($taxRateTransfer);
        }

        $productConcreteTransfer->setTaxSet($taxTransfer);
    }

    /**
     * @param string $sku
     *
     * @throws MissingProductException
     *
     * @return int
     */
    public function getProductAbstractIdByConcreteSku($sku)
    {
        $productConcrete = $this->productQueryContainer->queryProductConcreteBySku($sku)->findOne();

        if (!$productConcrete) {
            throw new MissingProductException(
                sprintf(
                    'Tried to retrieve a product concrete with sku %s, but it does not exist.',
                    $sku
                )
            );
        }

        return $productConcrete->getFkProductAbstract();
    }

    /**
     * @param string $sku
     *
     * @throws MissingProductException
     *
     * @return string
     */
    public function getAbstractSkuFromProductConcrete($sku)
    {
        $productConcrete = $this->productQueryContainer->queryProductConcreteBySku($sku)->findOne();

        if (!$productConcrete) {
            throw new MissingProductException(
                sprintf(
                    'Tried to retrieve a product concrete with sku %s, but it does not exist.',
                    $sku
                )
            );
        }

        return $productConcrete->getSpyProductAbstract()->getSku();
    }

    /**
     * @param array $attributes
     *
     * @return string
     */
    protected function encodeAttributes(array $attributes)
    {
        return json_encode($attributes);
    }

}