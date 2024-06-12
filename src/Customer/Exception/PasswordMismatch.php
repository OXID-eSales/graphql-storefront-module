<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Exception;

use OxidEsales\GraphQL\Base\Exception\Error;
use OxidEsales\GraphQL\Base\Exception\ErrorCategories;

final class PasswordMismatch extends Error
{
    public function getCategory(): string
    {
        return ErrorCategories::REQUESTERROR;
    }

    public static function byOldPassword(): self
    {
        return new self('Old password does not match our records');
    }
}
