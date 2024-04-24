<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\DataType;

use DateTimeImmutable;
use OxidEsales\GraphQL\Storefront\Banner\DataType\Banner;
use PHPUnit\Framework\TestCase;

/**
 * @covers \OxidEsales\GraphQL\Storefront\Banner\DataType\Banner
 */
final class BannerTest extends TestCase
{
    /**
     * @dataProvider activeDataProvider
     *
     * @param mixed $active
     * @param mixed $from
     * @param mixed $to
     * @param mixed $now
     * @param mixed $result
     */
    public function testActive($active, $from, $to, $now, $result): void
    {
        $banner = new Banner(
            $this->getModelStub(
                $active,
                $from,
                $to
            )
        );
        $this->assertSame($result, $banner->isActive($now));
    }

    public static function activeDataProvider()
    {
        return [
            [
                'active' => '1',
                'from' => '',
                'to' => '',
                'now' => null,
                'result' => true,
            ],
            [
                'active' => '0',
                'from' => '',
                'to' => '',
                'now' => null,
                'result' => false,
            ],
            [
                'active' => '1',
                'from' => '2018-01-01 12:00:00',
                'to' => '2018-01-01 19:00:00',
                'now' => null,
                'result' => true,
            ],
            [
                'active' => '0',
                'from' => '2018-01-01 12:00:00',
                'to' => '2018-01-01 19:00:00',
                'now' => null,
                'result' => false,
            ],
            [
                'active' => '0',
                'from' => '2018-01-01 12:00:00',
                'to' => '2018-01-01 19:00:00',
                'now' => new DateTimeImmutable('2018-01-01 16:00:00'),
                'result' => true,
            ],
        ];
    }

    private function getModelStub(
        string $active = '1',
        string $activefrom = '0000-00-00 00:00:00',
        string $activeto = '0000-00-00 00:00:00'
    ) {
        $model = $this->createPartialMock(
            \OxidEsales\Eshop\Application\Model\Actions::class,
            ['getRawFieldData']
        );
        $model->method('getRawFieldData')->willReturnMap([
            ['oxtype', Banner::ACTION_TYPE],
            ['oxactive', $active],
            ['oxactivefrom', $activefrom],
            ['oxactiveto', $activeto],
        ]);

        return $model;
    }
}
