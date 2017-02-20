<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace Spryker\Zed\CmsGui\Dependency\Facade;

use Generated\Shared\Transfer\UrlTransfer;

interface CmsGuiToUrlInterface
{

    /**
     * @param string $url
     *
     * @return bool
     */
    public function hasUrl($url);

    /**
     * @param \Generated\Shared\Transfer\UrlTransfer $urlTransfer
     *
     * @return \Generated\Shared\Transfer\UrlTransfer|null
     */
    public function findUrl(UrlTransfer $urlTransfer);

}