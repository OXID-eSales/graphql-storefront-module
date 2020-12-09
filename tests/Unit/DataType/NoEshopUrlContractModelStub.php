<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\DataType;

use OxidEsales\Eshop\Core\Model\BaseModel as EshopBaseModel;

final class NoEshopUrlContractModelStub extends EshopBaseModel
{
    public function __construct()
    {
    }
}
