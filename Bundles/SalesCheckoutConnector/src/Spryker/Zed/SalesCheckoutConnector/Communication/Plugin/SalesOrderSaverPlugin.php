<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\SalesCheckoutConnector\Communication\Plugin;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Checkout\Dependency\Plugin\CheckoutSaveOrderInterface;
use Spryker\Zed\SalesCheckoutConnector\Business\SalesCheckoutConnectorFacade;
use Spryker\Zed\SalesCheckoutConnector\Communication\SalesCheckoutConnectorCommunicationFactory;

/**
 * @method SalesCheckoutConnectorFacade getFacade()
 * @method SalesCheckoutConnectorCommunicationFactory getFactory()
 */
class SalesOrderSaverPlugin extends AbstractPlugin implements CheckoutSaveOrderInterface
{

    /**
     * @param OrderTransfer $orderTransfer
     * @param CheckoutResponseTransfer $checkoutResponse
     *
     * @return void
     */
    public function saveOrder(OrderTransfer $orderTransfer, CheckoutResponseTransfer $checkoutResponse)
    {
        $this->getFacade()->saveOrder($orderTransfer, $checkoutResponse);
    }

}