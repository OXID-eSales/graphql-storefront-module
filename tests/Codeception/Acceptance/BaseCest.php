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
        $this->activateModules();

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
    protected function activateModules(int $shopId = 1): void
    {
        $testConfig        = new \OxidEsales\TestingLibrary\TestConfig();
        $modulesToActivate = $testConfig->getModulesToActivate();

        if ($modulesToActivate) {
            $serviceCaller = new \OxidEsales\TestingLibrary\ServiceCaller();
            $serviceCaller->setParameter('modulestoactivate', $modulesToActivate);

            try {
                $serviceCaller->callService('ModuleInstaller', $shopId);
            } catch (ModuleSetupException $e) {
                // this may happen if the module is already active,
                // we can ignore this
            }
        }
    }
}
