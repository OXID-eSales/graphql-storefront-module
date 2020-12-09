<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Tests\Unit\DataType;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\BaseModel as EshopBaseModel;

final class ProductScalePriceModelStub extends EshopBaseModel
{
    public function __construct(
        string $addAbsolute,
        string $addPercentage,
        string $amountFrom,
        string $amountTo
    ) {
        $this->_sCoreTable = 'oxprice2article';

        $this->oxprice2article__oxaddabs = new Field(
            $addAbsolute,
            Field::T_RAW
        );
        $this->oxprice2article__oxaddperc = new Field(
            $addPercentage,
            Field::T_RAW
        );
        $this->oxprice2article__oxamount = new Field(
            $amountFrom,
            Field::T_RAW
        );
        $this->oxprice2article__oxamountto = new Field(
            $amountTo,
            Field::T_RAW
        );
    }
}
