<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\WishedPrice\Service;

use OxidEsales\GraphQL\Storefront\WishedPrice\Exception\WishedPriceOutOfBounds;
use OxidEsales\GraphQL\Storefront\WishedPrice\Service\WishedPriceInputValidator;
use OxidEsales\GraphQL\Storefront\WishedPrice\Service\WishedPriceInputValidatorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(WishedPriceInputValidator::class)]
final class WishedPriceInputValidatorTest extends TestCase
{
    #[Test]
    public function implementsValidatorInterface(): void
    {
        $this->assertInstanceOf(WishedPriceInputValidatorInterface::class, $this->getSut());
    }

    #[Test]
    #[DataProvider('validPriceProvider')]
    public function validatePricePassesForPositivePrice(float $price): void
    {
        $this->expectNotToPerformAssertions();

        $this->getSut()->validatePrice($price);
    }

    public static function validPriceProvider(): array
    {
        return [
            'typical price' => [9.99],
            'small positive' => [0.01],
            'large price' => [1000.0],
        ];
    }

    #[Test]
    #[DataProvider('outOfBoundsPriceProvider')]
    public function validatePriceThrowsForNonPositivePrice(float $price): void
    {
        $this->expectException(WishedPriceOutOfBounds::class);
        $this->expectExceptionMessage(sprintf('Wished price must be positive, was: %d', $price));

        $this->getSut()->validatePrice($price);
    }

    public static function outOfBoundsPriceProvider(): array
    {
        return [
            'zero' => [0.0],
            'negative' => [-1.0],
        ];
    }

    private function getSut(): WishedPriceInputValidator
    {
        return new WishedPriceInputValidator();
    }
}
