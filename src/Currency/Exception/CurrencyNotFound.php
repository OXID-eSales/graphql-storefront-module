<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Currency\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

use function sprintf;

final class CurrencyNotFound extends NotFound
{
    public function __construct(string $name = null)
    {
        $message = 'No active currency was found';

        if($name) {
            $message = sprintf('Currency "%s" was not found', $name);
        }

        parent::__construct($message);
    }
}
