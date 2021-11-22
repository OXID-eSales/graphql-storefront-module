<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance;

use Codeception\Scenario;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\Facts\Facts;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

$facts = new Facts();

require_once $facts->getVendorPath() . '/oxid-esales/testing-library/base.php';

abstract class MultishopBaseCest extends BaseCest
{
    protected const SUBSHOP_ID = 2;

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        $facts = new Facts();

        if (!$facts->isEnterprise()) {
            $scenario->skip('Skip EE related tests for CE/PE edition');

            return;
        }

        parent::_before($I, $scenario);

        $this->ensureSubshop();
    }

    public function _after(AcceptanceTester $I): void
    {
        $facts = new Facts();

        if ($facts->isEnterprise()) {
            $I->updateConfigInDatabaseForShops('blMallUsers', false, 'bool', [1, 2]);
        }
    }

    private function ensureSubshop(): void
    {
        $container         = ContainerFactory::getInstance()->getContainer();
        $shopConfiguration = $container->get(ShopConfigurationDaoInterface::class)->get(1);
        $container->get(ShopConfigurationDaoInterface::class)->save(
            $shopConfiguration,
            self::SUBSHOP_ID
        );

        $this->regenerateDatabaseViews();
        $this->activateModules(self::SUBSHOP_ID);
    }

    private function regenerateDatabaseViews(): void
    {
        $vendorPath = (new Facts())->getVendorPath();
        exec($vendorPath . '/bin/oe-eshop-db_views_regenerate');
    }
}
