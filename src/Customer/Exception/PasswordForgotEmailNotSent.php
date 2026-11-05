<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Exception;

use RuntimeException;

final class PasswordForgotEmailNotSent extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('The password forgot email could not be sent.');
    }
}
