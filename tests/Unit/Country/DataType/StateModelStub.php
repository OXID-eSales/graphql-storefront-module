<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Country\DataType;

use OxidEsales\Eshop\Application\Model\State as EshopStateModel;
use OxidEsales\Eshop\Core\Field;

final class StateModelStub extends EshopStateModel
{
    public function __construct()
    {
        $this->_sCoreTable = 'oxstates';
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
