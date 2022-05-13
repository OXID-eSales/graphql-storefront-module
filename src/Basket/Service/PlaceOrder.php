<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Service;

use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket as BasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\Event\BeforePlaceOrder;
use OxidEsales\GraphQL\Storefront\Basket\Exception\PlaceOrder as PlaceOrderException;
use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Basket as BasketInfrastructure;
use OxidEsales\GraphQL\Storefront\Basket\Service\Basket as BasketService;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;
use OxidEsales\GraphQL\Storefront\Customer\Service\Customer as CustomerService;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\DataType\DeliveryMethod as DeliveryMethodDataType;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\Exception\MissingDeliveryMethod;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\Exception\UnavailableDeliveryMethod;
use OxidEsales\GraphQL\Storefront\Order\DataType\Order as OrderDataType;
use OxidEsales\GraphQL\Storefront\Payment\DataType\Payment as PaymentDataType;
use OxidEsales\GraphQL\Storefront\Payment\Exception\MissingPayment;
use OxidEsales\GraphQL\Storefront\Payment\Exception\UnavailablePayment;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TheCodingMachine\GraphQLite\Types\ID;

final class PlaceOrder
{
    /** @var Authentication */
    private $authenticationService;

    /** @var Legacy */
    private $legacyService;

    /** @var CustomerService */
    private $customerService;

    /** @var BasketInfrastructure */
    private $basketInfrastructure;

    /** @var BasketService */
    private $basketService;

    /** @var BasketRelationService */
    private $basketRelationService;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        Authentication $authenticationService,
        Legacy $legacyService,
        CustomerService $customerService,
        BasketInfrastructure $basketInfrastructure,
        BasketRelationService $basketRelationService,
        BasketService $basketService,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->authenticationService = $authenticationService;
        $this->legacyService = $legacyService;
        $this->customerService = $customerService;
        $this->basketRelationService = $basketRelationService;
        $this->basketInfrastructure = $basketInfrastructure;
        $this->basketService = $basketService;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws UnavailableDeliveryMethod
     * @throws UnavailablePayment
     * @throws PlaceOrderException
     */
    public function placeOrder(
        ID $basketId,
        ?bool $termsAndConditions = null,
        ?string $remark = null
    ): OrderDataType {
        $this->eventDispatcher->dispatch(
            new BeforePlaceOrder(
                $basketId
            ),
            BeforePlaceOrder::class
        );

        $userBasket = $this->basketService->getAuthenticatedCustomerBasket($basketId);

        $this->checkTermsAndConditionsConsent($userBasket, $termsAndConditions);

        $basketItemsErrors = $this->basketInfrastructure->checkBasketItems($userBasket->getEshopModel());

        if ($basketItemsErrors) {
            throw PlaceOrderException::productsNotOrdarable();
        }

        /** @var ?DeliveryMethodDataType $deliveryMethod */
        $deliveryMethod = $this->basketRelationService->deliveryMethod($userBasket);

        if ($deliveryMethod === null) {
            throw MissingDeliveryMethod::provideDeliveryMethod();
        }

        if (!$this->basketService->isDeliveryMethodAvailableForBasket($userBasket->id(), $deliveryMethod->id())) {
            throw UnavailableDeliveryMethod::byId((string)$deliveryMethod->id()->val());
        }

        /** @var ?PaymentDataType $payment */
        $payment = $this->basketRelationService->payment($userBasket);

        if ($payment === null) {
            throw MissingPayment::providePayment();
        }

        if (!$this->basketService->isPaymentMethodAvailableForBasket($userBasket->id(), $payment->getId())) {
            throw UnavailablePayment::byId((string)$payment->getId()->val());
        }

        /** @var CustomerDataType $customer */
        $customer = $this->customerService->customer(
            (string)$this->authenticationService->getUser()->id()
        );

        return $this->basketInfrastructure->placeOrder(
            $customer,
            $userBasket,
            $remark
        );
    }

    private function checkTermsAndConditionsConsent(BasketDataType $basket, ?bool $termsAndConditions): void
    {
        $confirmTermsAndConditions = $this->legacyService->getConfigParam('blConfirmAGB');

        if (($confirmTermsAndConditions && !$termsAndConditions) || $termsAndConditions === false) {
            throw PlaceOrderException::notAcceptedTOS((string)$basket->id()->val());
        }
    }
}
