<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Service;

use OxidEsales\GraphQL\Base\DataType\IDFilter;
use OxidEsales\GraphQL\Base\DataType\PaginationFilter;
use OxidEsales\GraphQL\Storefront\Address\DataType\DeliveryAddress;
use OxidEsales\GraphQL\Storefront\Address\Exception\DeliveryAddressNotFound;
use OxidEsales\GraphQL\Storefront\Address\Service\DeliveryAddress as DeliveryAddressService;
use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketCost;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketItem;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketItemFilterList;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketOwner;
use OxidEsales\GraphQL\Storefront\Basket\Service\Basket as BasketService;
use OxidEsales\GraphQL\Storefront\Basket\Service\BasketItem as BasketItemService;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\DataType\DeliveryMethod;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\Exception\DeliveryMethodNotFound;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\Service\DeliveryMethod as DeliveryMethodService;
use OxidEsales\GraphQL\Storefront\Payment\DataType\Payment;
use OxidEsales\GraphQL\Storefront\Payment\Exception\PaymentNotFound;
use OxidEsales\GraphQL\Storefront\Payment\Service\Payment as PaymentService;
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

    /** @var DeliveryAddressService */
    private $deliveryAddressService;

    /** @var PaymentService */
    private $paymentService;

    /** @var DeliveryMethodService */
    private $deliveryMethodService;

    public function __construct(
        BasketItemService $basketItemService,
        BasketService $basketService,
        VoucherRepository $voucherRepository,
        DeliveryAddressService $deliveryAddressService,
        PaymentService $paymentService,
        DeliveryMethodService $deliveryMethodService
    ) {
        $this->deliveryAddressService = $deliveryAddressService;
        $this->paymentService         = $paymentService;
        $this->deliveryMethodService  = $deliveryMethodService;
        $this->basketItemService      = $basketItemService;
        $this->basketService          = $basketService;
        $this->voucherRepository      = $voucherRepository;
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

    /**
     * @Field()
     */
    public function deliveryAddress(Basket $basket): ?DeliveryAddress
    {
        $addressId = $basket->getDeliveryAddressId();

        if (empty($addressId->val())) {
            return null;
        }

        try {
            $deliveryAddress = $this->deliveryAddressService->getDeliveryAddress($addressId);
        } catch (DeliveryAddressNotFound $e) {
            $deliveryAddress = null;
        }

        return $deliveryAddress;
    }

    /**
     * Returns selected payment for current basket.
     *
     * @Field()
     */
    public function payment(Basket $basket): ?Payment
    {
        $paymentId = $basket->getPaymentId()->val();

        if (empty($paymentId)) {
            return null;
        }

        try {
            $payment = $this->paymentService->payment((string) $paymentId);
        } catch (PaymentNotFound $e) {
            $payment = null;
        }

        return $payment;
    }

    /**
     * Returns selected delivery method for current basket.
     *
     * @Field()
     */
    public function deliveryMethod(Basket $basket): ?DeliveryMethod
    {
        $deliveryMethodId = (string) $basket->getDeliveryMethodId();

        if (empty($deliveryMethodId)) {
            return null;
        }

        try {
            $deliveryMethod = $this->deliveryMethodService->getDeliveryMethod($deliveryMethodId);
        } catch (DeliveryMethodNotFound $e) {
            $deliveryMethod = null;
        }

        return $deliveryMethod;
    }
}
