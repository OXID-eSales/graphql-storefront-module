<?php
// phpcs:ignoreFile

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance;

use Codeception\Scenario;
use OxidEsales\Eshop\Application\Model\Shop;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Model\ListModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Edition\Edition;
use OxidEsales\EshopCommunity\Internal\Framework\Edition\EditionDirectoriesLocator;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectDirectoriesLocator;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopEnterprise\Application\Controller\Admin\ShopMain;
use OxidEsales\EshopEnterprise\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationGeneratorBridgeInterface;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;
use Psr\Container\ContainerInterface;

abstract class MultishopBaseCest extends BaseCest
{
    protected const SUBSHOP_ID = 2;

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        if (!(new EditionDirectoriesLocator())->getEditionRootPath(Edition::Enterprise)){
            $scenario->skip('Skip EE related tests for CE/PE edition');

            return;
        }

        parent::_before($I, $scenario);

        $this->ensureSubshop();
    }

    public function _after(AcceptanceTester $I): void
    {
        parent::_after($I);

        if ((new EditionDirectoriesLocator())->getEditionRootPath(Edition::Enterprise)){
            $I->updateInDatabase('oxconfig', ['oxvarvalue' => false], ['oxvarname' => 'blMallUsers']);
        }
    }

    private function ensureSubshop(): void
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $shopConfiguration = $container->get(ShopConfigurationDaoBridgeInterface::class)->get();
        Registry::getConfig()->setShopId(self::SUBSHOP_ID);
        $container->get(ShopConfigurationDaoBridgeInterface::class)->save($shopConfiguration);
        $container->get(ShopConfigurationGeneratorBridgeInterface::class)->generateForShop(self::SUBSHOP_ID);

        $shop = oxNew(Shop::class);
        $shop->load(1);
        $shop->assign(
            [
                'oxid' => self::SUBSHOP_ID
            ]
        );
        $shop->save();

        #$this->copyConfig();
        $this->copyContent();
        $this->regenerateDatabaseViews();
        $this->activateModule($container);

        ContainerFactory::resetContainer();
    }

    private function regenerateDatabaseViews(): void
    {
        exec((new ProjectDirectoriesLocator())->getVendorPath() . '/bin/oe-eshop-db_views_generate');
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
        $shopContentList = oxNew(ListModel::class);
        $shopContentList->init("oxi18n", 'oxcontents');
        $shopContentList->getBaseObject()->setEnableMultilang(false);

        $shopContentList->selectString("select * from oxcontents where oxshopid = '1'");
        foreach ($shopContentList as $shopContent) {
            $shopContent->oxcontents__oxshopid->setValue(self::SUBSHOP_ID);
            $shopContent->setId();
            $shopContent->save();
        }
    }

    private function copyConfig(): void
    {
        $utilsObject = Registry::getUtilsObject();
        $db = DatabaseProvider::getDb();

        $selectShopConfigurationQuery =
            "select oxvarname, oxvartype, oxvarvalue, oxmodule
            from oxconfig where oxshopid = '1'";

        $shopConfiguration = $db->select($selectShopConfigurationQuery);
        if ($shopConfiguration != false && $shopConfiguration->count() > 0) {
            while (!$shopConfiguration->EOF) {
                $newId = $utilsObject->generateUID();
                $insertNewConfigQuery =
                    "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue, oxmodule)
                     values (:oxid, :oxshopid, :oxvarname, :oxvartype, :value, :oxmodule)";
                $db->execute($insertNewConfigQuery, [
                    ':oxid' => $newId,
                    ':oxshopid' => self::SUBSHOP_ID,
                    ':oxvarname' => $shopConfiguration->fields[0],
                    ':oxvartype' => $shopConfiguration->fields[1],
                    ':value' => $shopConfiguration->fields[2],
                    ':oxmodule' => $shopConfiguration->fields[3],
                ]);
            }
            $shopConfiguration->fetchRow();
        }
    }
}
