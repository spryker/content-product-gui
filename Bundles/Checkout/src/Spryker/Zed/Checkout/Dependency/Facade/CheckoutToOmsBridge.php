<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Checkout\Dependency\Facade;

use Spryker\Zed\Oms\Business\OmsFacade;

class CheckoutToOmsBridge implements CheckoutToOmsInterface
{

    /**
     * @var OmsFacade
     */
    protected $omsFacade;

    /**
     * SalesToOmsBridge constructor.
     *
     * @param OmsFacade $omsFacade
     */
    public function __construct($omsFacade)
    {
        $this->omsFacade = $omsFacade;
    }

    /**
     * @param array $orderItemIds
     * @param array $data
     *
     * @return array
     */
    public function triggerEventForNewOrderItems(array $orderItemIds, array $data = [])
    {
        return $this->omsFacade->triggerEventForNewOrderItems($orderItemIds, $data);
    }

}