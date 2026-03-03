<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Country\DataType;

use OxidEsales\Eshop\Application\Model\State as EshopStateModel;
use OxidEsales\GraphQL\Storefront\Country\DataType\State;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\OxidEsales\GraphQL\Storefront\Country\DataType\State::class)]
final class StateTest extends TestCase
{
    public function testState(): void
    {
        $model = new StateModelStub();
        $data = [
            'oxtitle' => 'state title',
            'oxisoalpha2' => 'state isoalpha2',
            'oxtimestamp' => '2020-10-10',
        ];
        $model->assign($data);
        $dataType = new State($model);

        $this->assertInstanceOf(
            EshopStateModel::class,
            $dataType->getEshopModel()
        );
        $this->assertIsString($dataType->getTitle());
        $this->assertIsString($dataType->getIsoAlpha2());
        $this->assertIsObject($dataType->getCreationDate());
        $this->assertSame(
            $dataType->getTitle(),
            $model->getRawFieldData('oxtitle')
        );
        $this->assertSame(
            $dataType->getIsoAlpha2(),
            $model->getRawFieldData('oxisoalpha2')
        );
        $this->assertNotEmpty(
            $dataType->getCreationDate()
        );
    }
}
