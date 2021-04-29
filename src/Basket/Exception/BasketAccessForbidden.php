<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Exception;

use OxidEsales\GraphQL\Base\Exception\InvalidToken;

final class BasketAccessForbidden extends InvalidToken
{
    public static function byAuthenticatedUser(): self
    {
        return new self(
            'You are not allowed to access this basket as it belongs to somebody else'
        );
    }

    public static function basketIsPrivate(): self
    {
        return new self('Basket is private.');
    }
}
