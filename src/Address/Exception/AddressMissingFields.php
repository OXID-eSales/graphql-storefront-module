<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Exception;

use OxidEsales\GraphQL\Base\Exception\Error;

final class AddressMissingFields extends Error
{
    protected $category = 'validation';

    public function __construct(
        string $addressName,
        array $missingFields
    ) {
        $message = sprintf('%s address is missing required fields: %s',
            ucfirst($addressName),
            implode(', ', $missingFields)
        );
        parent::__construct($message);
    }
}
