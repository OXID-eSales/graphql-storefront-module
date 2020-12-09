<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Service;

use OxidEsales\GraphQL\Base\DataType\IDFilter;
use OxidEsales\GraphQL\Base\DataType\PaginationFilter;
use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketCost;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketItem;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketItemFilterList;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketOwner;
use OxidEsales\GraphQL\Storefront\Basket\Service\Basket as BasketService;
use OxidEsales\GraphQL\Storefront\Basket\Service\BasketItem as BasketItemService;
use OxidEsales\GraphQL\Storefront\Voucher\DataType\Voucher;
use OxidEsales\GraphQL\Storefront\Voucher\Infrastructure\Repository as VoucherRepository;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=Basket::class)
 */
final class BasketRelationService
{
    /** @var BasketItemService */
    private $basketItemService;

    /** @var BasketService */
    private $basketService;

    /** @var VoucherRepository */
    private $voucherRepository;

    public function __construct(
        BasketItemService $basketItemService,
        BasketService $basketService,
        VoucherRepository $voucherRepository
    ) {
        $this->basketItemService    = $basketItemService;
        $this->basketService        = $basketService;
        $this->voucherRepository    = $voucherRepository;
    }

    /**
     * @Field()
     */
    public function owner(Basket $basket): BasketOwner
    {
        return $this->basketService->basketOwner((string) $basket->getUserId());
    }

    /**
     * @Field()
     *
     * @return BasketItem[]
     */
    public function items(
        Basket $basket,
        ?PaginationFilter $pagination
    ): array {
        return $this->basketItemService->basketItems(
            new BasketItemFilterList(
                new IDFilter($basket->id())
            ),
            $pagination
        );
    }

    /**
     * @Field()
     */
    public function cost(Basket $basket): BasketCost
    {
        return $this->basketService->basketCost($basket);
    }

    /**
     * @Field()
     *
     * @return Voucher[]
     */
    public function vouchers(Basket $basket): array
    {
        return $this->voucherRepository->getBasketVouchers((string) $basket->id());
    }
}
