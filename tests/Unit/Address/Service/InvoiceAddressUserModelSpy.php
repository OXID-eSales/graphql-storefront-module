<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Address\Service;

use OxidEsales\Eshop\Application\Model\User as EshopUserModel;

/**
 * Test double exposing the module-extension method setAutomaticUserGroups(),
 * which is not declared on the unified eshop user model and therefore cannot
 * be mocked directly in a unit test.
 */
class InvoiceAddressUserModelSpy extends EshopUserModel
{
    public function setAutomaticUserGroups(): void
    {
    }
}
