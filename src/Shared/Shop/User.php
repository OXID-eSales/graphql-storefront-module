<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Shop;

/**
 * User model extended
 *
 * @mixin User
 * @eshopExtension
 */
class User extends User_parent
{
    public function setAutomaticUserGroups(): void
    {
        $this->_setAutoGroups((string)$this->getRawFieldData('oxcountryid'));
    }
}
