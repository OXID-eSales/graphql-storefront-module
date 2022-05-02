<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Exception;

use OxidEsales\GraphQL\Base\Exception\Error;

/**
 * @deprecated 3.0.0
 * @see AddressMissingFields
 */
final class DeliveryAddressMissingFields extends Error
{
    public function getCategory(): string
    {
        return 'validation';
    }

    /**
     * @param string[] $missingFields
     */
    public static function byFields(array $missingFields): AddressMissingFields
    {
        return new AddressMissingFields('delivery', $missingFields);
    }
}
