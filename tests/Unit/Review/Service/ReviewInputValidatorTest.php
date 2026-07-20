<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Review\Service;

use OxidEsales\GraphQL\Storefront\Review\Exception\RatingOutOfBounds;
use OxidEsales\GraphQL\Storefront\Review\Exception\ReviewInputInvalid;
use OxidEsales\GraphQL\Storefront\Review\Input\ReviewInputInterface;
use OxidEsales\GraphQL\Storefront\Review\Service\ReviewInputValidator;
use OxidEsales\GraphQL\Storefront\Review\Service\ReviewInputValidatorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ReviewInputValidator::class)]
final class ReviewInputValidatorTest extends TestCase
{
    #[Test]
    public function implementsValidatorInterface(): void
    {
        $this->assertInstanceOf(ReviewInputValidatorInterface::class, $this->getSut());
    }

    #[Test]
    #[DataProvider('validRatingProvider')]
    public function validateRatingPassesForRatingWithinBounds(?int $rating): void
    {
        $this->expectNotToPerformAssertions();

        $this->getSut()->validateRating($rating);
    }

    public static function validRatingProvider(): array
    {
        return [
            'lower bound' => [1],
            'middle' => [3],
            'upper bound' => [5],
            'null is allowed' => [null],
        ];
    }

    #[Test]
    #[DataProvider('outOfBoundsRatingProvider')]
    public function validateRatingThrowsForRatingOutOfBounds(int $rating): void
    {
        $this->expectException(RatingOutOfBounds::class);
        $this->expectExceptionMessage(sprintf('Rating must be between 1 and 5, was %s', $rating));

        $this->getSut()->validateRating($rating);
    }

    public static function outOfBoundsRatingProvider(): array
    {
        return [
            'below lower bound' => [0],
            'above upper bound' => [6],
        ];
    }

    #[Test]
    public function validateReviewInputThrowsWhenRatingNullAndTextEmpty(): void
    {
        $this->expectException(ReviewInputInvalid::class);
        $this->expectExceptionMessage('Review input cannot have both empty text and rating value.');

        $this->getSut()->validateReviewInput($this->getInputStub(text: '', rating: null));
    }

    #[Test]
    #[DataProvider('validReviewInputProvider')]
    public function validateReviewInputPassesWhenRatingOrTextPresent(?string $text, ?int $rating): void
    {
        $this->expectNotToPerformAssertions();

        $this->getSut()->validateReviewInput($this->getInputStub(text: $text, rating: $rating));
    }

    public static function validReviewInputProvider(): array
    {
        return [
            'rating set, empty text' => ['', 4],
            'rating set, null text' => [null, 4],
            'text set, null rating' => ['great product', null],
            'both set' => ['great product', 4],
        ];
    }

    private function getSut(): ReviewInputValidator
    {
        return new ReviewInputValidator();
    }

    private function getInputStub(?string $text, ?int $rating): ReviewInputInterface
    {
        $input = $this->createStub(ReviewInputInterface::class);
        $input->method('getProductId')->willReturn(uniqid());
        $input->method('getText')->willReturn($text);
        $input->method('getRating')->willReturn($rating);

        return $input;
    }
}
