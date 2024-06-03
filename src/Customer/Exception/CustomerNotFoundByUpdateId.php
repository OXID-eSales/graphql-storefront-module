<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Exception;

use OxidEsales\GraphQL\Base\Exception\Error;
use OxidEsales\GraphQL\Base\Exception\ErrorCategories;

final class CustomerNotFoundByUpdateId extends Error
{
    public function __construct(string $passwordUpdateId)
    {
        parent::__construct("No customer was found by update id: \"$passwordUpdateId\".");
    }

    public function getCategory(): string
    {
        return ErrorCategories::REQUESTERROR;
    }

}
