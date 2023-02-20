<?php
// phpcs:ignoreFile

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance;

use Codeception\Scenario;
use OxidEsales\Eshop\Core\Exception\DatabaseErrorException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopEnterprise\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationGeneratorBridgeInterface;
use OxidEsales\Facts\Facts;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;
use Psr\Container\ContainerInterface;

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
        parent::_after($I);

        $facts = new Facts();
        if ($facts->isEnterprise()) {
            $I->updateConfigInDatabaseForShops('blMallUsers', false, 'bool', [1, 2]);
        }
    }

    private function ensureSubshop(): void
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $shopConfiguration = $container->get(ShopConfigurationDaoBridgeInterface::class)->get();
        Registry::getConfig()->setShopId(self::SUBSHOP_ID);
        $container->get(ShopConfigurationDaoBridgeInterface::class)->save($shopConfiguration);
        $container->get(ShopConfigurationGeneratorBridgeInterface::class)->generateForShop(self::SUBSHOP_ID);

        $this->copyContent();
        $this->regenerateDatabaseViews();
        $this->activateModule($container);

        ContainerFactory::resetContainer();
    }

    private function regenerateDatabaseViews(): void
    {
        $vendorPath = (new Facts())->getVendorPath();
        exec($vendorPath . '/bin/oe-eshop-db_views_generate');
    }

    private function activateModule(ContainerInterface $container)
    {
        $container
            ->get(ModuleActivationBridgeInterface::class)
            ->activate('oe_graphql_base', self::SUBSHOP_ID);
        $container
            ->get(ModuleActivationBridgeInterface::class)
            ->activate('oe_graphql_storefront', self::SUBSHOP_ID);
    }

    private function copyContent()
    {
        //copy contents
        $shopContentList = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
        $shopContentList->init("oxi18n", 'oxcontents');
        $shopContentList->getBaseObject()->setEnableMultilang(false);

        $shopContentList->selectString("select * from oxcontents where oxshopid = '1'");
        foreach ($shopContentList as $shopContent) {
            try {
                $shopContent->oxcontents__oxshopid->setValue(self::SUBSHOP_ID);
                $shopContent->delete();
                $shopContent->setId();
                $shopContent->save();
            } catch (DatabaseErrorException $e) {
                // This happen on executing multiple tests
            }
        }
    }
}
