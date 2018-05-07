<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CustomerAccess\Persistence;

use Generated\Shared\Transfer\CustomerAccessTransfer;

interface CustomerAccessEntityManagerInterface
{
    /**
     * @param string $contentType
     * @param bool $hasAccess
     *
     * @return \Generated\Shared\Transfer\CustomerAccessTransfer
     */
    public function createCustomerAccess($contentType, $hasAccess): CustomerAccessTransfer;

    /**
     * @param \Generated\Shared\Transfer\CustomerAccessTransfer $customerAccessTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerAccessTransfer
     */
    public function updateUnauthenticatedCustomerAccess(CustomerAccessTransfer $customerAccessTransfer): CustomerAccessTransfer;
}
