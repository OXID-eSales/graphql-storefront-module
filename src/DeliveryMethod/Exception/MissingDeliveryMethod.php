<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\DeliveryMethod\Exception;

use OxidEsales\GraphQL\Base\Exception\Error;
use OxidEsales\GraphQL\Base\Exception\ErrorCategories;
use OxidEsales\GraphQL\Base\Exception\HttpErrorInterface;

final class MissingDeliveryMethod extends Error implements HttpErrorInterface
{
    public function getHttpStatus(): int
    {
        return 400;
    }

    public function getCategory(): string
    {
        return ErrorCategories::REQUESTERROR;
    }

    public static function provideDeliveryMethod(): self
    {
        return new self('Delivery set must be selected!');
    }
}
