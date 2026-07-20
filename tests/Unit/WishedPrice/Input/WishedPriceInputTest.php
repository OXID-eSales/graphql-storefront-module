<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\WishedPrice\Input;

use OxidEsales\GraphQL\Storefront\WishedPrice\Input\WishedPriceInput;
use OxidEsales\GraphQL\Storefront\WishedPrice\Input\WishedPriceInputInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TheCodingMachine\GraphQLite\Types\ID;

#[CoversClass(WishedPriceInput::class)]
final class WishedPriceInputTest extends TestCase
{
    #[Test]
    public function gettersReturnConstructorValues(): void
    {
        $sut = $this->getSut(
            productId: $productId = new ID(uniqid()),
            currencyName: $currencyName = uniqid(),
            price: $price = 12.34,
        );

        $this->assertSame($productId, $sut->getProductId());
        $this->assertSame((string)$productId->val(), (string)$sut->getProductId()->val());
        $this->assertSame($currencyName, $sut->getCurrencyName());
        $this->assertSame($price, $sut->getPrice());
    }

    #[Test]
    public function implementsInputInterface(): void
    {
        $this->assertInstanceOf(WishedPriceInputInterface::class, $this->getSut());
    }

    private function getSut(
        ?ID $productId = null,
        ?string $currencyName = null,
        ?float $price = null,
    ): WishedPriceInput {
        return new WishedPriceInput(
            productId: $productId ?? new ID(uniqid()),
            currencyName: $currencyName ?? uniqid(),
            price: $price ?? 1.0,
        );
    }
}
