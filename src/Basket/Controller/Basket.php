<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Controller;

use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket as BasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\DataType\PublicBasket as PublicBasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketModify;
use OxidEsales\GraphQL\Storefront\Basket\Service\Basket as BasketService;
use OxidEsales\GraphQL\Storefront\Basket\Service\PlaceOrder as PlaceOrderService;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\DataType\BasketDeliveryMethod as BasketDeliveryMethodDataType;
use OxidEsales\GraphQL\Storefront\Order\DataType\Order as OrderDataType;
use OxidEsales\GraphQL\Storefront\Payment\DataType\BasketPayment;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Right;
use TheCodingMachine\GraphQLite\Types\ID;

final class Basket
{
    /** @var BasketService */
    private $basketService;

    /** @var PlaceOrderService */
    private $placeOrderService;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        BasketService $basketService,
        PlaceOrderService $placeOrderService,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->basketService     = $basketService;
        $this->placeOrderService = $placeOrderService;
        $this->eventDispatcher   = $eventDispatcher;
    }

    /**
     * @Query()
     * @Logged()
     */
    public function basket(ID $basketId): BasketDataType
    {
        return $this->basketService->basket($basketId);
    }

    /**
     * @Query()
     */
    public function publicBasket(ID $basketId): PublicBasketDataType
    {
        return $this->basketService->publicBasket($basketId);
    }

    /**
     * @Mutation()
     * @Right("ADD_PRODUCT_TO_BASKET")
     */
    public function basketAddItem(ID $basketId, ID $productId, float $amount): BasketDataType
    {
        return $this->basketService->addBasketItem($basketId, $productId, $amount);
    }

    /**
     * @Mutation()
     * @Right("REMOVE_BASKET_PRODUCT")
     */
    public function basketRemoveItem(ID $basketId, ID $basketItemId, float $amount): BasketDataType
    {
        return $this->basketService->removeBasketItem($basketId, $basketItemId, $amount);
    }

    /**
     * @Mutation()
     * @Right("CREATE_BASKET")
     */
    public function basketCreate(BasketDataType $basket): BasketDataType
    {
        $this->basketService->store($basket);

        return $basket;
    }

    /**
     * @Mutation()
     * @Logged()
     */
    public function basketRemove(ID $basketId): bool
    {
        return $this->basketService->remove($basketId);
    }

    /**
     * @Mutation()
     * @Logged()
     */
    public function basketMakePublic(ID $basketId): BasketDataType
    {
        return $this->basketService->makePublic($basketId);
    }

    /**
     * @Mutation()
     * @Logged()
     */
    public function basketMakePrivate(ID $basketId): BasketDataType
    {
        return $this->basketService->makePrivate($basketId);
    }

    /**
     * Argument `owner` will be matched exactly against lastname and / or email
     * Query for public baskets by owner.
     *
     * @Query()
     *
     * @return PublicBasketDataType[]
     */
    public function baskets(string $owner): array
    {
        return $this->basketService->publicBasketsByOwnerNameOrEmail(
            $owner
        );
    }

    /**
     * @Mutation()
     * @Right("ADD_VOUCHER")
     */
    public function basketAddVoucher(ID $basketId, string $voucherNumber): BasketDataType
    {
        return $this->basketService->addVoucher($basketId, $voucherNumber);
    }

    /**
     * @Mutation()
     * @Right("REMOVE_VOUCHER")
     */
    public function basketRemoveVoucher(ID $basketId, ID $voucherId): BasketDataType
    {
        return $this->basketService->removeVoucher($basketId, $voucherId);
    }

    /**
     * @Mutation()
     * @Logged()
     */
    public function basketSetDeliveryAddress(ID $basketId, ?ID $deliveryAddressId): BasketDataType
    {
        $event = new BeforeBasketModify($basketId, BeforeBasketModify::TYPE_SET_DELIVERY_ADDRESS);
        $this->eventDispatcher->dispatch($event);

        return $this->basketService->setDeliveryAddress($basketId, $deliveryAddressId);
    }

    /**
     * @Mutation()
     * @Logged()
     */
    public function basketSetPayment(ID $basketId, ID $paymentId): BasketDataType
    {
        $event = new BeforeBasketModify($basketId, BeforeBasketModify::TYPE_SET_PAYMENT_METHOD);
        $this->eventDispatcher->dispatch($event);

        return $this->basketService->setPayment($basketId, $paymentId);
    }

    /**
     * @Mutation()
     * @Logged()
     */
    public function basketSetDeliveryMethod(ID $basketId, ID $deliveryMethodId): BasketDataType
    {
        $event = new BeforeBasketModify($basketId, BeforeBasketModify::TYPE_SET_DELIVERY_METHOD);
        $this->eventDispatcher->dispatch($event);

        return $this->basketService->setDeliveryMethod($basketId, $deliveryMethodId);
    }

    /**
     * @Query
     * @Logged()
     *
     * @return BasketDeliveryMethodDataType[]
     */
    public function basketDeliveryMethods(ID $basketId): array
    {
        return $this->basketService->getBasketDeliveryMethods($basketId);
    }

    /**
     * Returns all payments that can be used for particular basket.
     *
     * @Query
     * @Logged()
     *
     * @return BasketPayment[]
     */
    public function basketPayments(ID $basketId): array
    {
        return $this->basketService->getBasketPayments($basketId);
    }

    /**
     * @Mutation()
     * @Right("PLACE_ORDER")
     */
    public function placeOrder(ID $basketId, ?bool $confirmTermsAndConditions = null): OrderDataType
    {
        return $this->placeOrderService->placeOrder($basketId, $confirmTermsAndConditions);
    }
}
