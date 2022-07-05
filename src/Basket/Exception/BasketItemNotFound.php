<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

use function sprintf;

final class BasketItemNotFound extends NotFound
{
    public function __construct(string $basketItemId, ?string $basketId = null)
    {
        $message = sprintf('Basket item was not found by id: %s', $basketItemId);

        if ($basketId) {
            $message = sprintf('Basket item with id %s not found in your basket %s', $basketItemId, $basketId);
        }

        parent::__construct($message);
    }
}
