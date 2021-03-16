<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Payment\Exception;

use OxidEsales\GraphQL\Base\Exception\Error;

final class PaymentValidationFailed extends Error
{
    public function getCategory(): string
    {
        return 'validation';
    }

    public static function byDeliveryMethod(): self
    {
        return new self('Delivery method must be provided!');
    }
}
