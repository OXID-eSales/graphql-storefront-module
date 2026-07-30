<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Review\Input;

use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Input;

#[Input(name: 'ReviewInput', default: true)]
final class ReviewInput implements ReviewInputInterface
{
    #[Field]
    private string $productId;

    #[Field]
    private ?string $text;

    #[Field]
    private ?int $rating;

    public function __construct(
        string $productId,
        ?string $text,
        ?int $rating
    ) {
        $this->productId = $productId;
        $this->text = $text;
        $this->rating = $rating;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }
}
