<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Tests\Integration\Service;

use OxidEsales\Eshop\Core\Price as EshopPriceModel;
use OxidEsales\Eshop\Core\Registry as EshopRegistry;
use OxidEsales\GraphQL\Catalogue\Currency\Infrastructure\Repository as CurrencyRepository;
use OxidEsales\GraphQL\Catalogue\Shared\DataType\Price as PriceDataType;
use OxidEsales\GraphQL\Catalogue\Shared\Service\PriceRelationService;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers OxidEsales\GraphQL\Catalogue\Shared\DataType\Price
 */
final class PriceRelationTest extends TestCase
{
    public function testDefaultCurrency(): void
    {
        $priceRelation = new PriceRelationService(new CurrencyRepository(EshopRegistry::getConfig()));

        $price         = oxNew(EshopPriceModel::class);
        $priceDataType = new PriceDataType($price);

        $currency = $priceRelation->getCurrency($priceDataType);

        $this->assertSame('EUR', $currency->getName());
    }

    public function testInjectedCurrency(): void
    {
        $priceRelation = new PriceRelationService(new CurrencyRepository(EshopRegistry::getConfig()));

        $currencyObject       = new stdClass();
        $currencyObject->name = 'XYZ';

        $price         = oxNew(EshopPriceModel::class);
        $priceDataType = new PriceDataType($price, $currencyObject);

        $currency = $priceRelation->getCurrency($priceDataType);

        $this->assertSame('XYZ', $currency->getName());
    }
}
