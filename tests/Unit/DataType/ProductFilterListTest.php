<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\DataType;

use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Storefront\Product\DataType\ProductFilterList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use TheCodingMachine\GraphQLite\Types\ID;

#[CoversClass(\OxidEsales\GraphQL\Storefront\Product\DataType\ProductFilterList::class)]
final class ProductFilterListTest extends TestCase
{
    public function testInputFilterDefaults(): void
    {
        $filter = new ProductFilterList();
        $this->assertEquals(
            [
                'oxtitle' => null,
                'oxcatnid' => null,
                'oxmanufacturerid' => null,
                'oxvendorid' => null,
                'oxparentid' => new IDFilter(new ID('')),
            ],
            $filter->getFilters()
        );
    }
}
