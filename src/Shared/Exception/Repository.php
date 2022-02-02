<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Exception;

use RuntimeException;

final class Repository extends RuntimeException
{
    public static function cannotDeleteModel(): self
    {
        return new self('Failed deleting object');
    }

    public static function cannotSaveModel(): self
    {
        return new self('Object save failed');
    }
}
