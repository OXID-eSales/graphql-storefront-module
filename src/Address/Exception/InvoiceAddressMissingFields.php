<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Exception;

use Exception;
use GraphQL\Error\ClientAware;
use OxidEsales\GraphQL\Base\Exception\HttpErrorInterface;

final class InvoiceAddressMissingFields extends Exception implements ClientAware, HttpErrorInterface
{
    public function getHttpStatus(): int
    {
        return 400;
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'validation';
    }

    /**
     * @param string[] $missingFields
     */
    public static function byFields(array $missingFields): self
    {
        return new self(
            'Invoice address is missing required fields: ' . implode(', ', $missingFields)
        );
    }
}
