<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Address;

use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\Eshop\Core\Field;

final class InvoiceAddressModelStub extends EshopUserModel
{
    public function __construct()
    {
        $this->_sCoreTable = 'oxuser';
    }

    /**
     * @param mixed $data
     * @var array
     *
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
