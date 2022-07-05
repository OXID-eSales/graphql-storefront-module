<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Attribute\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

final class AttributeNotFound extends NotFound
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Attribute was not found by id: %s', $id));
    }
}
