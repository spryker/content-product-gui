<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Country\Zed;

use Generated\Shared\Transfer\CountryCollectionTransfer;
use Generated\Shared\Transfer\CountryRequestTransfer;
use Generated\Shared\Transfer\CountryTransfer;
use Spryker\Client\Country\Dependency\Client\CountryToZedRequestClientInterface;

class CountryStub implements CountryStubInterface
{
    /**
     * @var \Spryker\Client\Country\Dependency\Client\CountryToZedRequestClientInterface
     */
    protected $zedRequestClient;

    /**
     * @param \Spryker\Client\Country\Dependency\Client\CountryToZedRequestClientInterface $zedRequestClient
     */
    public function __construct(CountryToZedRequestClientInterface $zedRequestClient)
    {
        $this->zedRequestClient = $zedRequestClient;
    }

    /**
     * @param \Generated\Shared\Transfer\CountryTransfer $countryTransfer
     *
     * @return \Generated\Shared\Transfer\CountryTransfer
     */
    public function getCountryByIso2Code(CountryTransfer $countryTransfer): CountryTransfer
    {
        /** @var \Generated\Shared\Transfer\CountryTransfer $countryTransfer */
        $countryTransfer = $this->zedRequestClient->call('/country/gateway/get-country-by-iso2-code', $countryTransfer);

        return $countryTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CountryRequestTransfer $countryRequestTransfer
     *
     * @return \Generated\Shared\Transfer\CountryCollectionTransfer
     */
    public function findCountriesByIso2Codes(CountryRequestTransfer $countryRequestTransfer): CountryCollectionTransfer
    {
        /** @var \Generated\Shared\Transfer\CountryCollectionTransfer $countriesCollectionTransfer */
        $countriesCollectionTransfer = $this->zedRequestClient->call('/country/gateway/find-countries-by-iso2-codes', $countryRequestTransfer);

        return $countriesCollectionTransfer;
    }
}
