<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Translation\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

use function sprintf;

final class TranslationNotFound extends NotFound
{
    public function __construct(string $key)
    {
        parent::__construct(sprintf('Translation was not found by key: %s', $key));
    }
}
