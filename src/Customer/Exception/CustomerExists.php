<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Exception;

use OxidEsales\GraphQL\Base\Exception\Exists;

final class CustomerExists extends Exists
{
    public static function byEmail(string $email): self
    {
        return new self(sprintf("This e-mail address '%s' already exists!", $email));
    }
}
