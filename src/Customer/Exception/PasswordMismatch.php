<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Exception;

use OxidEsales\GraphQL\Base\Exception\Error;
use OxidEsales\GraphQL\Base\Exception\ErrorCategories;
use OxidEsales\GraphQL\Base\Exception\HttpErrorInterface;

final class PasswordMismatch extends Error implements HttpErrorInterface
{
    public function getHttpStatus(): int
    {
        return 403;
    }

    public function getCategory(): string
    {
        return ErrorCategories::REQUESTERROR;
    }

    public static function byOldPassword(): self
    {
        return new self('Old password does not match our records');
    }

    public static function byLength(): self
    {
        return new self('Password does not match length requirements');
    }
}
