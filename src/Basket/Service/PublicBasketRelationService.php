<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Service;

use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketItem;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketItemFilterList;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketOwner;
use OxidEsales\GraphQL\Storefront\Basket\DataType\PublicBasket as PublicBasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\Service\Basket as BasketService;
use OxidEsales\GraphQL\Storefront\Basket\Service\BasketItem as BasketItemService;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=PublicBasketDataType::class)
 */
final class PublicBasketRelationService
{
    /** @var BasketItemService */
    private $basketItemService;

    /** @var BasketService */
    private $basketService;

    public function __construct(
        BasketItemService $basketItemService,
        BasketService $basketService
    ) {
        $this->basketItemService = $basketItemService;
        $this->basketService = $basketService;
    }

    /**
     * @Field()
     */
    public function owner(PublicBasketDataType $basket): BasketOwner
    {
        return $this->basketService->basketOwner((string)$basket->getUserId());
    }

    /**
     * @Field()
     *
     * @return BasketItem[]
     */
    public function items(
        PublicBasketDataType $basket,
        ?Pagination $pagination
    ): array {
        return $this->basketItemService->basketItems(
            new BasketItemFilterList(
                new IDFilter($basket->id())
            ),
            $pagination
        );
    }
}
