<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Controller;

use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket as BasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\Service\Basket as BasketService;
use OxidEsales\GraphQL\Storefront\Basket\Service\PlaceOrder as PlaceOrderService;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\DataType\BasketDeliveryMethod as BasketDeliveryMethodDataType;
use OxidEsales\GraphQL\Storefront\Order\DataType\Order as OrderDataType;
use OxidEsales\GraphQL\Storefront\Payment\DataType\BasketPayment;
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

    public function __construct(
        BasketService $basketService,
        PlaceOrderService $placeOrderService
    ) {
        $this->basketService     = $basketService;
        $this->placeOrderService = $placeOrderService;
    }

    /**
     * @Query()
     */
    public function basket(string $id): BasketDataType
    {
        return $this->basketService->basket($id);
    }

    /**
     * @Mutation()
     * @Logged()
     */
    public function basketAddProduct(string $basketId, string $productId, float $amount): BasketDataType
    {
        return $this->basketService->addProduct($basketId, $productId, $amount);
    }

    /**
     * @Mutation()
     * @Logged()
     */
    public function basketRemoveProduct(string $basketId, string $productId, int $amount): BasketDataType
    {
        return $this->basketService->removeProduct($basketId, $productId, $amount);
    }

    /**
     * @Mutation()
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
    public function basketRemove(string $id): bool
    {
        return $this->basketService->remove($id);
    }

    /**
     * @Mutation()
     * @Logged()
     */
    public function basketMakePublic(string $id): BasketDataType
    {
        return $this->basketService->makePublic($id);
    }

    /**
     * @Mutation()
     * @Logged()
     */
    public function basketMakePrivate(string $id): BasketDataType
    {
        return $this->basketService->makePrivate($id);
    }

    /**
     * Argument `owner` will be matched against lastname and / or email
     *
     * @Query()
     *
     * @return BasketDataType[]
     */
    public function baskets(string $owner): array
    {
        return $this->basketService->publicBasketsByOwnerNameOrEmail(
            $owner
        );
    }

    /**
     * @Mutation()
     * @Logged()
     */
    public function basketAddVoucher(string $basketId, string $voucherNumber): BasketDataType
    {
        return $this->basketService->addVoucher($basketId, $voucherNumber);
    }

    /**
     * @Mutation()
     * @Logged()
     */
    public function basketRemoveVoucher(string $basketId, string $voucherId): BasketDataType
    {
        return $this->basketService->removeVoucher($basketId, $voucherId);
    }

    /**
     * @Mutation()
     * @Logged()
     */
    public function basketSetDeliveryAddress(string $basketId, string $deliveryAddressId): BasketDataType
    {
        return $this->basketService->setDeliveryAddress($basketId, $deliveryAddressId);
    }

    /**
     * @Mutation()
     * @Logged()
     */
    public function basketSetPayment(ID $basketId, ID $paymentId): BasketDataType
    {
        return $this->basketService->setPayment($basketId, $paymentId);
    }

    /**
     * @Mutation()
     * @Logged()
     */
    public function basketSetDeliveryMethod(ID $basketId, ID $deliveryMethodId): BasketDataType
    {
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
     * @Logged()
     */
    public function placeOrder(ID $basketId, ?bool $confirmTermsAndConditions = null): OrderDataType
    {
        return $this->placeOrderService->placeOrder($basketId, $confirmTermsAndConditions);
    }
}
