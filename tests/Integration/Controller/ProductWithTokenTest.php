<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Tests\Integration\Controller;

use DateTimeImmutable;
use OxidEsales\GraphQL\Base\Tests\Integration\TokenTestCase;
use TheCodingMachine\GraphQLite\Types\DateTimeType;

final class ProductWithTokenTest extends TokenTestCase
{
    private const ACTIVE_PRODUCT = '058e613db53d782adfc9f2ccb43c45fe';

    private const INACTIVE_PRODUCT  = '09602cddb5af0aba745293d08ae6bcf6';

    protected function setUp(): void
    {
        parent::setUp();

        $this->prepareToken();
    }

    public function testGetSingleActiveProduct(): void
    {
        $result = $this->query('query {
            product (id: "' . self::ACTIVE_PRODUCT . '") {
                id
                active
                title
            }
        }');

        $this->assertEquals(200, $result['status']);

        $product = $result['body']['data']['product'];

        $this->assertSame(self::ACTIVE_PRODUCT, $product['id']);
        $this->assertSame(true, $product['active']);

        $dateTimeType = DateTimeType::getInstance();
        //Fixture timestamp can have few seconds difference
        $this->assertLessThanOrEqual(
            $dateTimeType->serialize(new DateTimeImmutable('now')),
            $result['body']['data']['product']['timestamp']
        );

        $this->assertEmpty(array_diff(array_keys($product), [
            'id',
            'active',
            'title',
        ]));
    }

    public function testGetInactiveProduct(): void
    {
        $result = $this->query('query {
            product (id: "' . self::INACTIVE_PRODUCT . '") {
                id
            }
        }');

        $this->assertEquals(200, $result['status']);

        $this->assertEquals(
            [
                'id' => self::INACTIVE_PRODUCT,
            ],
            $result['body']['data']['product']
        );
    }

    public function testGetSingleNonExistingProduct(): void
    {
        $result = $this->query('query {
            product (id: "DOES-NOT-EXIST") {
                id
            }
        }');

        $this->assertEquals(404, $result['status']);
    }

    public function testGetAllProducts(): void
    {
        $result = $this->query('query {
            products {
                id
            }
        }');

        $this->assertEquals(200, $result['status']);

        $this->assertCount(
            54,
            $result['body']['data']['products']
        );
    }
}
