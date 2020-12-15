<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\File\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

final class FileNotFound extends NotFound
{
    public static function byId(string $id): self
    {
        return new self(sprintf('File was not found by id: %s', $id));
    }
}
