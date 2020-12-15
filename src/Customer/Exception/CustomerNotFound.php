<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

use function sprintf;

final class CustomerNotFound extends NotFound
{
    public static function byId(string $id): self
    {
        return new self(sprintf('Customer was not found by id: %s', $id));
    }

    public static function byEmail(string $email): self
    {
        return new self(sprintf('Customer was not found for: %s', $email));
    }
}
