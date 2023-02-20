<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance;

use Codeception\Scenario;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

abstract class BaseCest
{
    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
    }

    public function _after(AcceptanceTester $I): void
    {
        $I->logout();
    }
}
