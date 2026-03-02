<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ContentProductGui\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\ContentProductGui\Communication\ContentProductGuiCommunicationFactory getFactory()
 */
class ProductAbstractController extends AbstractController
{
    /**
     * @var string
     */
    public const PARAM_IDS = 'ids';

    public function productAbstractSelectedTableAction(Request $request): JsonResponse
    {
        $idProductAbstracts = $request->query->all()[static::PARAM_IDS] ?? [];

        return $this->jsonResponse(
            $this->getFactory()->createProductAbstractSelectedTable($idProductAbstracts)->fetchData(),
        );
    }

    public function productAbstractViewTableAction(Request $request): JsonResponse
    {
        return $this->jsonResponse(
            $this->getFactory()->createProductAbstractViewTable()->fetchData(),
        );
    }
}
