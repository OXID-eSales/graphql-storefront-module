<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Exception;

use Exception;

final class GraphQLServiceNotFound extends Exception
{
    public static function byServiceName(string $name): self
    {
        return new self(sprintf('GraphQL service %s is not available.', $name));
    }
}
