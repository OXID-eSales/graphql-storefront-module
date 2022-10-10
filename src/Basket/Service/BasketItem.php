<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Service;

use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination as PaginationFilter;
use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket as BasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketItem as BasketItemDataType;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketItemFilterList;
use OxidEsales\GraphQL\Storefront\Basket\Event\AfterAddItem;
use OxidEsales\GraphQL\Storefront\Basket\Event\AfterRemoveItem;
use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeAddItem;
use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeRemoveItem;
use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Basket as BasketInfrastructure;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TheCodingMachine\GraphQLite\Types\ID;

final class BasketItem
{
    /** @var Repository */
    private $repository;

    private EventDispatcherInterface $eventDispatcher;

    private BasketFinder $basketFinderService;

    private ProductService $productService;

    private BasketInfrastructure $basketInfrastructure;

    public function __construct(
        Repository $repository,
        EventDispatcherInterface $eventDispatcher,
        BasketFinder $basketFinderService,
        ProductService $productService,
        BasketInfrastructure $basketInfrastructure
    ) {
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
        $this->basketFinderService = $basketFinderService;
        $this->productService = $productService;
        $this->basketInfrastructure = $basketInfrastructure;
    }

    /**
     * @return BasketItemDataType[]
     */
    public function basketItems(BasketItemFilterList $filter, ?PaginationFilter $pagination = null): array
    {
        return $this->repository->getByFilter(
            $filter,
            BasketItemDataType::class,
            $pagination
        );
    }

    public function addItemToBasket(ID $basketId, ID $productId, float $amount): BasketDataType
    {
        $this->eventDispatcher->dispatch(
            new BeforeAddItem($basketId, $productId, $amount),
            BeforeAddItem::class
        );

        $basket = $this->basketFinderService->getAuthenticatedCustomerBasket($basketId);

        $this->productService->product($productId);

        $event = new BeforeAddItem(
            $basketId,
            $productId,
            $amount
        );

        $this->eventDispatcher->dispatch(
            $event,
            BeforeAddItem::class
        );

        $this->basketInfrastructure->addBasketItem($basket, $productId, $amount);

        $this->eventDispatcher->dispatch(
            new AfterAddItem($basketId, $productId, $amount),
            AfterAddItem::class
        );

        return $basket;
    }

    public function removeItemFromBasket(ID $basketId, ID $basketItemId, float $amount): BasketDataType
    {
        $basket = $this->basketFinderService->getAuthenticatedCustomerBasket($basketId);

        $event = new BeforeRemoveItem(
            $basketId,
            $basketItemId,
            $amount
        );

        $this->eventDispatcher->dispatch(
            $event,
            BeforeRemoveItem::class
        );

        $this->basketInfrastructure->removeBasketItem($basket, $basketItemId, $event->getAmount());

        $this->eventDispatcher->dispatch(
            new AfterRemoveItem($basketId, $basketItemId, $amount),
            AfterRemoveItem::class
        );

        return $basket;
    }
}
