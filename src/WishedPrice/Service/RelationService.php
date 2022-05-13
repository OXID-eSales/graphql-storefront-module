<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Service;

use OxidEsales\GraphQL\Storefront\Currency\DataType\Currency;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Storefront\Shared\DataType\Price;
use OxidEsales\GraphQL\Storefront\WishedPrice\DataType\Inquirer as InquirerDataType;
use OxidEsales\GraphQL\Storefront\WishedPrice\DataType\WishedPrice;
use OxidEsales\GraphQL\Storefront\WishedPrice\Exception\InquirerNotFound;
use OxidEsales\GraphQL\Storefront\WishedPrice\Infrastructure\PriceFactory;
use OxidEsales\GraphQL\Storefront\WishedPrice\Service\Inquirer as InquirerService;
use stdClass;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=WishedPrice::class)
 */
final class RelationService
{
    /** @var InquirerService */
    private $inquirerService;

    /** @var ProductService */
    private $productService;

    /** @var PriceFactory */
    private $priceFactory;

    public function __construct(
        InquirerService $inquirerService,
        ProductService $productService,
        PriceFactory $priceFactory
    ) {
        $this->inquirerService = $inquirerService;
        $this->productService = $productService;
        $this->priceFactory = $priceFactory;
    }

    /**
     * @Field()
     */
    public function getInquirer(WishedPrice $wishedPrice): ?InquirerDataType
    {
        try {
            return $this->inquirerService->inquirer((string)$wishedPrice->getInquirerId());
        } catch (InquirerNotFound $e) {
            return null;
        }
    }

    /**
     * @Field()
     */
    public function getProduct(WishedPrice $wishedPrice): Product
    {
        return $this->productService->product(
            $wishedPrice->getProductId()
        );
    }

    /**
     * @Field()
     */
    public function getPrice(WishedPrice $wishedPrice): Price
    {
        return $this->priceFactory->createPrice($wishedPrice);
    }

    /**
     * @Field()
     */
    public function getCurrency(WishedPrice $wishedPrice): Currency
    {
        /** @var stdClass $currency */
        $currency = $wishedPrice->getEshopModel()->getPriceAlarmCurrency();

        return new Currency($currency);
    }
}
