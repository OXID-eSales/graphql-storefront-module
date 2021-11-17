<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Country\DataType;

use OxidEsales\Eshop\Application\Model\State as EshopStateModel;
use OxidEsales\GraphQL\Storefront\Country\DataType\State;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \OxidEsales\GraphQL\Storefront\Country\DataType\State
 */
final class StateTest extends TestCase
{
    public function testState(): void
    {
        $model = new StateModelStub();
        $data  = [
            'oxtitle'     => 'state title',
            'oxisoalpha2' => 'state isoalpha2',
            'oxtimestamp' => '2020-10-10',
        ];
        $model->assign($data);
        $dataType = new State($model);

        $this->assertInstanceOf(
            EshopStateModel::class,
            $dataType->getEshopModel()
        );
        $this->assertThat(
            $dataType->getTitle(),
            $this->isType(IsType::TYPE_STRING)
        );
        $this->assertThat(
            $dataType->getIsoAlpha2(),
            $this->isType(IsType::TYPE_STRING)
        );
        $this->assertThat(
            $dataType->getCreationDate(),
            $this->isType(IsType::TYPE_OBJECT)
        );
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
