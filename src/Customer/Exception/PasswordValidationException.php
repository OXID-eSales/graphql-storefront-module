<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Exception;

use OxidEsales\GraphQL\Base\Exception\Error;
use OxidEsales\GraphQL\Base\Exception\ErrorCategories;

final class PasswordValidationException extends Error
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function getCategory(): string
    {
        return ErrorCategories::REQUESTERROR;
    }

}
