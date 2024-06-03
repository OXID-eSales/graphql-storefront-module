<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Infrastructure;

use OxidEsales\Eshop\Core\Model\BaseModel;

class OxNewFactory implements OxNewFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function getModel(string $class): BaseModel
    {
        return oxNew($class);
    }
}
