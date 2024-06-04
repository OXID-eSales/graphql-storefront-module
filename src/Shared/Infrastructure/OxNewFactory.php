<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Infrastructure;

class OxNewFactory implements OxNewFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function getModel(string $class): object
    {
        return oxNew($class);
    }
}
