<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

class_alias(
    \OxidEsales\Eshop\Application\Model\Basket::class,
    \OxidEsales\GraphQL\Storefront\Shared\Shop\Basket_parent::class
);
class_alias(
    OxidEsales\Eshop\Application\Model\User::class,
    \OxidEsales\GraphQL\Storefront\Shared\Shop\User_parent::class
);
class_alias(
    \OxidEsales\Eshop\Application\Model\Voucher::class,
    \OxidEsales\GraphQL\Storefront\Shared\Shop\Voucher_parent::class
);
class_alias(
    \OxidEsales\Eshop\Core\Language::class,
    \OxidEsales\GraphQL\Storefront\Shared\Shop\Language_parent::class
);
