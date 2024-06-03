<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Infrastructure;

use OxidEsales\Eshop\Core\Model\BaseModel;

interface OxNewFactoryInterface
{
    /**
     * @param class-string<BaseModel> $class
     *
     * @return BaseModel
     */
    public function getModel(string $class): BaseModel;

}
