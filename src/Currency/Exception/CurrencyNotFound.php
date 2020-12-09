<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Currency\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

use function sprintf;

final class CurrencyNotFound extends NotFound
{
    public static function byActiveInShop(): self
    {
        return new self(sprintf('No active currency was found'));
    }

    public static function byName(string $name): self
    {
        return new self(sprintf('Currency "%s" was not found', $name));
    }
}
