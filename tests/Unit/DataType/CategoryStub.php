<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\DataType;

use OxidEsales\Eshop\Application\Model\Category as EshopCategoryModel;
use OxidEsales\Eshop\Core\Field;

final class CategoryStub extends EshopCategoryModel
{
    public function __construct(
        string $active = '1',
        string $activefrom = '0000-00-00 00:00:00',
        string $activeto = '0000-00-00 00:00:00'
    ) {
        $this->_sCoreTable = 'oxcategories';

        $this->oxcategories__oxactive = new Field(
            $active,
            Field::T_RAW
        );
        $this->oxcategories__oxactivefrom = new Field(
            $activefrom,
            Field::T_RAW
        );
        $this->oxcategories__oxactiveto = new Field(
            $activeto,
            Field::T_RAW
        );
    }
}
