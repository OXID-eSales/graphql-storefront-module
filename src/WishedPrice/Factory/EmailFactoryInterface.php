<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Factory;

use OxidEsales\Eshop\Core\Email;

interface EmailFactoryInterface
{
    public function create(): Email;
}
