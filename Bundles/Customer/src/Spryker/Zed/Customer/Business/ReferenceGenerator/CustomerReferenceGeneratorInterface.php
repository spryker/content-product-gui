<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Customer\Business\ReferenceGenerator;

use Generated\Shared\Transfer\CustomerTransfer;

interface CustomerReferenceGeneratorInterface
{

    /**
     * @param CustomerTransfer $orderTransfer
     *
     * @return string
     */
    public function generateCustomerReference(CustomerTransfer $orderTransfer);

}