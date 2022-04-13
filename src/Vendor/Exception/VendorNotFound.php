<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Vendor\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

final class VendorNotFound extends NotFound
{
    public static function byId(string $id): self
    {
        return new self(sprintf('Vendor was not found by id: %s', $id));
    }

    public static function bySlug(string $slug): self
    {
        return new self(sprintf('Vendor was not found by slug: %s', $slug));
    }

    public static function byParameter(): self
    {
        return new self(sprintf('Please provide id xor slug to query Vendor'));
    }

    public static function byAmbiguousBySlug(string $slug): self
    {
        return new self(sprintf('Ambiguous slug: %s', $slug));
    }
}
