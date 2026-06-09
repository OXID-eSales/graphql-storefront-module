<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Country\Exception;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;

use function sprintf;

final class CountryIsInactive extends InvalidLogin
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Country is inactive: %s', $id));
    }
}
