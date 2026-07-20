<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Review\Input;

use OxidEsales\GraphQL\Storefront\Review\Input\ReviewInput;
use OxidEsales\GraphQL\Storefront\Review\Input\ReviewInputInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ReviewInput::class)]
final class ReviewInputTest extends TestCase
{
    #[Test]
    public function gettersReturnConstructorValues(): void
    {
        $sut = $this->getSut(
            productId: $productId = uniqid(),
            text: $text = uniqid(),
            rating: $rating = 4,
        );

        $this->assertSame($productId, $sut->getProductId());
        $this->assertSame($text, $sut->getText());
        $this->assertSame($rating, $sut->getRating());
    }

    #[Test]
    public function nullableFieldsPreserveNull(): void
    {
        $sut = $this->getSut(
            productId: $productId = uniqid(),
            text: null,
            rating: null,
        );

        $this->assertSame($productId, $sut->getProductId());
        $this->assertNull($sut->getText());
        $this->assertNull($sut->getRating());
    }

    #[Test]
    public function implementsInputInterface(): void
    {
        $this->assertInstanceOf(ReviewInputInterface::class, $this->getSut());
    }

    private function getSut(
        ?string $productId = null,
        ?string $text = null,
        ?int $rating = null,
    ): ReviewInput {
        return new ReviewInput(
            productId: $productId ?? uniqid(),
            text: $text,
            rating: $rating,
        );
    }
}
