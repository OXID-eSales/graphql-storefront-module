<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Exception;

use OxidEsales\GraphQL\Base\Exception\Error;
use OxidEsales\GraphQL\Base\Exception\ErrorCategories;

final class BasketItemAmountLimitedStock extends Error
{
    public function __construct(string $message = '', array $extensions = [])
    {
        parent::__construct($message, 200, null, ErrorCategories::REQUESTERROR, $extensions);
    }

    public static function limitedAvailability(string $productId, float $amount, ?string $basketItemId = null): self
    {
        $extensions = [
            'type'      => 'LIMITEDAVAILABILITY',
            'productId' => $productId,
        ];

        if ($basketItemId) {
            $extensions['basketItemId'] = $basketItemId;
        }

        return new self(sprintf('Not enough items of product with id %s in stock! Available: %d', $productId, $amount), $extensions);
    }

    public static function notAvailable(string $productId): self
    {
        return new self(sprintf('Product with id %s is not available', $productId), [
            'type' => 'NOTAVAILABLE',
        ]);
    }

    public static function productOutOfStock(string $productId): self
    {
        return new self(sprintf('Product with id %s is out of stock', $productId), [
            'type' => 'OUTOFSTOCK',
        ]);
    }
}
