<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Delivery\DataType;

use OxidEsales\Eshop\Application\Model\Address as EshopAddressModel;
use OxidEsales\Eshop\Core\Field;

final class DeliveryAddressModelStub extends EshopAddressModel
{
    public function __construct()
    {
        $this->_sCoreTable = 'oxaddress';
    }

    /**
     * @var array
     *
     * @param mixed $data
     */
    public function assign($data): void
    {
        foreach ($data as $k => $v) {
            $this->{$this->_sCoreTable . '__' . $k} = new Field(
                $v,
                Field::T_RAW
            );
        }
    }
}
