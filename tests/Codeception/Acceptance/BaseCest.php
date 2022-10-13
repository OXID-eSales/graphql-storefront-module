<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance;

use Codeception\Scenario;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

abstract class BaseCest
{
    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        //Some voucher tests are too fast so give 1 minute extra reservation time gap
        $I->updateConfigInDatabase('iVoucherTimeout', (time() - 60), 'int');
    }

    public function _after(AcceptanceTester $I): void
    {
        $I->logout();
    }

    /**
     * Activates modules
     */
    protected function activateModules(AcceptanceTester $I, int $shopId = 1): void
    {
        $I->activateModule($this->testModule1Id);
    }
}
