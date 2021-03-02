<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\DeliveryMethod\Exception;

use OxidEsales\GraphQL\Base\Exception\Error;
use OxidEsales\GraphQL\Base\Exception\ErrorCategories;

final class MissingDeliveryMethod extends Error
{
    public function getCategory(): string
    {
        return ErrorCategories::REQUESTERROR;
    }

    public static function provideDeliveryMethod(): self
    {
        return new self('Delivery set must be selected!');
    }
}
