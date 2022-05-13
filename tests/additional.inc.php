<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Facts\Facts;

$serviceCaller = oxNew(\OxidEsales\TestingLibrary\ServiceCaller::class);
$testConfig = oxNew(\OxidEsales\TestingLibrary\TestConfig::class);

$testDirectory = $testConfig->getEditionTestsPath($testConfig->getShopEdition());
$serviceCaller->setParameter('importSql', '@' . $testDirectory . '/Fixtures/testdemodata.sql');
$serviceCaller->callService('ShopPreparation', 1);

$serviceCaller->setParameter('importSql', '@' . __DIR__ . '/Fixtures/integrationtest.sql');
$serviceCaller->callService('ShopPreparation', 1);

if ((new Facts())->isEnterprise()) {
    $serviceCaller->setParameter('importSql', '@' . __DIR__ . '/Fixtures/integrationtest_ee.sql');
    $serviceCaller->callService('ShopPreparation', 1);
}

if (getenv('STOREFRONT_COVERAGE')) {
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
}
