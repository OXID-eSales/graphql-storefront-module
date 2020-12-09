<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Storefront\Address\DataType\AddressFilterList;
use OxidEsales\GraphQL\Storefront\Address\DataType\DeliveryAddress as DeliveryAddressDataType;
use OxidEsales\GraphQL\Storefront\Address\Exception\DeliveryAddressNotFound;
use OxidEsales\GraphQL\Storefront\Address\Service\DeliveryAddress as DeliveryAddressService;
use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket as BasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketCost;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketOwner as BasketOwnerDataType;
use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketAccessForbidden;
use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketNotFound;
use OxidEsales\GraphQL\Storefront\Basket\Exception\PlaceOrder;
use OxidEsales\GraphQL\Storefront\Basket\Exception\PlaceOrder as PlaceOrderException;
use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Basket as BasketInfraService;
use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Basket as BasketInfrastructure;
use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Repository as BasketRepository;
use OxidEsales\GraphQL\Storefront\Basket\Service\Basket as StorefrontBasketService;

use OxidEsales\GraphQL\Storefront\Country\DataType\Country as CountryDataType;
use OxidEsales\GraphQL\Storefront\Country\Service\Country as CountryService;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotFound;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Customer as CustomerInfrastructure;
use OxidEsales\GraphQL\Storefront\Customer\Service\Customer as CustomerService;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\DataType\BasketDeliveryMethod as BasketDeliveryMethodDataType;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\DataType\DeliveryMethod as DeliveryMethodDataType;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\Exception\MissingDeliveryMethod;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\Exception\UnavailableDeliveryMethod;
use OxidEsales\GraphQL\Storefront\Order\DataType\Order as OrderDataType;
use OxidEsales\GraphQL\Storefront\Payment\DataType\BasketPayment;
use OxidEsales\GraphQL\Storefront\Payment\DataType\Payment as PaymentDataType;
use OxidEsales\GraphQL\Storefront\Payment\Exception\MissingPayment;
use OxidEsales\GraphQL\Storefront\Payment\Exception\PaymentValidationFailed;
use OxidEsales\GraphQL\Storefront\Payment\Exception\UnavailablePayment;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Basket as SharedInfrastructure;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Voucher\DataType\Voucher as VoucherDataType;
use OxidEsales\GraphQL\Storefront\Voucher\Infrastructure\Repository as VoucherRepository;
use OxidEsales\GraphQL\Storefront\Voucher\Infrastructure\Voucher as VoucherInfrastructure;
use TheCodingMachine\GraphQLite\Types\ID;

final class Basket
{
    /** @var Repository */
    private $repository;

    /** @var BasketRepository */
    private $basketRepository;

    /** @var Authentication */
    private $authenticationService;

    /** @var Authorization */
    private $authorizationService;

    /** @var Legacy */
    private $legacyService;

    /** @var BasketInfraService */
    private $basketInfraService;

    /** @var ProductService */
    private $productService;

    /** @var SharedInfrastructure */
    private $sharedInfrastructure;

    /** @var BasketVoucher */
    private $basketVoucherService;

    /** @var VoucherInfrastructure */
    private $voucherInfrastructure;

    /** @var VoucherRepository */
    private $voucherRepository;

    /** @var BasketInfrastructure */
    private $basketInfrastructure;

    /** @var CountryService */
    private $countryService;

    /** @var CustomerService */
    private $customerService;

    /** @var StorefrontBasketService */
    private $accountBasketService;

    /** @var CustomerInfrastructure */
    private $customerInfrastructure;

    /** @var DeliveryAddressService */
    private $deliveryAddressService;

    /** @var BasketRelationService */
    private $basketRelationService;

    public function __construct(
        Repository $repository,
        BasketRepository $basketRepository,
        Authentication $authenticationService,
        Authorization $authorizationService,
        Legacy $legacyService,
        BasketInfraService $basketInfraService,
        ProductService $productService,
        SharedInfrastructure $sharedInfrastructure,
        BasketVoucher $basketVoucherService,
        VoucherInfrastructure $voucherInfrastructure,
        BasketRelationService $basketRelationService,
        CustomerInfrastructure $customerInfrastructure,
        BasketInfrastructure $basketInfrastructure,
        DeliveryAddressService $deliveryAddressService,
        VoucherRepository $voucherRepository,
        StorefrontBasketService $accountBasketService,
        CountryService $countryService,
        CustomerService $customerService
    ) {
        $this->repository             = $repository;
        $this->basketRepository       = $basketRepository;
        $this->authenticationService  = $authenticationService;
        $this->authorizationService   = $authorizationService;
        $this->legacyService          = $legacyService;
        $this->basketInfraService     = $basketInfraService;
        $this->productService         = $productService;
        $this->sharedInfrastructure   = $sharedInfrastructure;
        $this->basketVoucherService   = $basketVoucherService;
        $this->voucherInfrastructure  = $voucherInfrastructure;
        $this->voucherRepository      = $voucherRepository;
        $this->basketRelationService  = $basketRelationService;
        $this->basketInfrastructure   = $basketInfrastructure;
        $this->deliveryAddressService = $deliveryAddressService;
        $this->customerInfrastructure = $customerInfrastructure;
        $this->accountBasketService   = $accountBasketService;
        $this->countryService         = $countryService;
        $this->customerService        = $customerService;
    }

    /**
     * @throws BasketNotFound
     * @throws InvalidToken
     */
    public function basket(string $id): BasketDataType
    {
        $basket = $this->basketRepository->getBasketById($id);

        if ($basket->public() === false &&
            !$basket->belongsToUser($this->authenticationService->getUserId())
        ) {
            throw new InvalidToken('Basket is private.');
        }

        $this->sharedInfrastructure->getBasket($basket);

        return $basket;
    }

    /**
     * @throws BasketAccessForbidden
     * @throws BasketNotFound
     * @throws InvalidToken
     */
    public function getAuthenticatedCustomerBasket(string $id): BasketDataType
    {
        $basket = $this->basketRepository->getBasketById($id);
        $userId = $this->authenticationService->getUserId();

        if (!$basket->belongsToUser($userId)) {
            throw BasketAccessForbidden::byAuthenticatedUser();
        }

        return $basket;
    }

    public function basketByOwnerAndTitle(CustomerDataType $customer, string $title): BasketDataType
    {
        return $this->basketRepository->customerBasketByTitle($customer, $title);
    }

    /**
     * @return BasketDataType[]
     */
    public function basketsByOwner(CustomerDataType $customer): array
    {
        return $this->basketRepository->customerBaskets($customer);
    }

    /**
     * @throws BasketNotFound
     * @throws InvalidToken
     */
    public function remove(string $id): bool
    {
        $basket = $this->basketRepository->getBasketById($id);

        //user can remove only his own baskets unless otherwise authorized
        if (
            $this->authorizationService->isAllowed('DELETE_BASKET')
            || $basket->belongsToUser($this->authenticationService->getUserId())
        ) {
            $vouchers = $this->voucherRepository->getBasketVouchers($id);

            /** @var VoucherDataType $voucher */
            foreach ($vouchers as $voucher) {
                $this->voucherInfrastructure->removeVoucher($voucher, $basket);
            }

            return $this->repository->delete($basket->getEshopModel());
        }

        throw new InvalidLogin('Unauthorized');
    }

    /**
     * @throws CustomerNotFound
     */
    public function basketOwner(string $id): BasketOwnerDataType
    {
        $ignoreSubShop = (bool) $this->legacyService->getConfigParam('blMallUsers');

        try {
            /** @var BasketOwnerDataType $customer */
            $customer = $this->repository->getById(
                $id,
                BasketOwnerDataType::class,
                $ignoreSubShop
            );
        } catch (NotFound $e) {
            throw CustomerNotFound::byId($id);
        }

        return $customer;
    }

    public function addProduct(string $basketId, string $productId, float $amount): BasketDataType
    {
        $basket = $this->getAuthenticatedCustomerBasket($basketId);

        $this->productService->product($productId);

        $this->basketInfraService->addProduct($basket, $productId, $amount);

        return $basket;
    }

    public function removeProduct(string $basketId, string $productId, float $amount): BasketDataType
    {
        $basket = $this->getAuthenticatedCustomerBasket($basketId);

        $this->basketInfraService->removeProduct($basket, $productId, $amount);

        return $basket;
    }

    /**
     * @throws InvalidLogin
     * @throws InvalidToken
     */
    public function store(BasketDataType $basket): bool
    {
        return $this->repository->saveModel($basket->getEshopModel());
    }

    /**
     * @return BasketDataType[]
     */
    public function publicBasketsByOwnerNameOrEmail(string $owner): array
    {
        return $this->basketRepository->publicBasketsByOwnerNameOrEmail($owner);
    }

    /**
     * @throws BasketAccessForbidden
     */
    public function makePublic(string $basketId): BasketDataType
    {
        $basket = $this->getAuthenticatedCustomerBasket($basketId);

        $this->basketInfraService->makePublic($basket);

        return $basket;
    }

    /**
     * @throws BasketAccessForbidden
     */
    public function makePrivate(string $basketId): BasketDataType
    {
        $basket = $this->getAuthenticatedCustomerBasket($basketId);

        $this->basketInfraService->makePrivate($basket);

        return $basket;
    }

    public function basketCost(BasketDataType $basket): BasketCost
    {
        $basketModel = $this->sharedInfrastructure->getCalculatedBasket($basket);

        return new BasketCost($basketModel);
    }

    public function addVoucher(string $basketId, string $voucherNumber): BasketDataType
    {
        $basket = $this->basketRepository->getBasketById($basketId);

        if (!$basket->belongsToUser($this->authenticationService->getUserId())) {
            throw BasketAccessForbidden::byAuthenticatedUser();
        }

        $this->basketVoucherService->addVoucherToBasket($voucherNumber, $basket);

        return $basket;
    }

    public function removeVoucher(string $basketId, string $voucherId): BasketDataType
    {
        $basket = $this->basketRepository->getBasketById($basketId);

        if (!$basket->belongsToUser($this->authenticationService->getUserId())) {
            throw BasketAccessForbidden::byAuthenticatedUser();
        }

        $this->basketVoucherService->removeVoucherFromBasket($voucherId, $basket);

        return $basket;
    }

    /**
     * @throws BasketAccessForbidden
     * @throws BasketNotFound
     * @throws DeliveryAddressNotFound
     * @throws InvalidToken
     */
    public function setDeliveryAddress(string $basketId, string $deliveryAddressId): BasketDataType
    {
        $basket = $this->accountBasketService->getAuthenticatedCustomerBasket($basketId);

        if (!$this->deliveryAddressBelongsToUser($deliveryAddressId)) {
            throw DeliveryAddressNotFound::byId($deliveryAddressId);
        }

        $this->basketInfrastructure->setDeliveryAddress($basket, $deliveryAddressId);

        return $basket;
    }

    /**
     * @throws PaymentValidationFailed
     * @throws UnavailablePayment
     */
    public function setPayment(ID $basketId, ID $paymentId): BasketDataType
    {
        if (!$this->isPaymentMethodAvailableForBasket($basketId, $paymentId)) {
            throw UnavailablePayment::byId((string) $paymentId->val());
        }

        return $this->setPaymentIdBasket($basketId, $paymentId);
    }

    /**
     * @throws UnavailableDeliveryMethod
     */
    public function setDeliveryMethod(ID $basketId, ID $deliveryMethodId): BasketDataType
    {
        if (!$this->isDeliveryMethodAvailableForBasket($basketId, $deliveryMethodId)) {
            throw UnavailableDeliveryMethod::byId((string) $deliveryMethodId->val());
        }

        return $this->setDeliveryMethodIdToBasket($basketId, $deliveryMethodId);
    }

    /**
     * Check if payment method is available for user basket with concrete id
     */
    public function isPaymentMethodAvailableForBasket(ID $basketId, ID $paymentId): bool
    {
        $basket           = $this->accountBasketService->getAuthenticatedCustomerBasket((string) $basketId->val());
        $deliveryMethodId = $basket->getEshopModel()->getFieldData('oegql_deliverymethodid');

        if (!$deliveryMethodId) {
            throw PaymentValidationFailed::byDeliveryMethod();
        }

        $customer = $this->customerService->customer((string) $basket->getUserId()->val());
        $country  = $this->getBasketDeliveryCountryId($basket);

        $deliveries = $this->basketInfrastructure->getBasketAvailableDeliveryMethods(
            $customer,
            $basket,
            $country
        );

        $paymentMethods = isset($deliveries[$deliveryMethodId]) ? $deliveries[$deliveryMethodId]->getPaymentTypes() : [];

        return array_key_exists((string) $paymentId->val(), $paymentMethods);
    }

    /**
     * Updates payment id for the user basket
     */
    public function setPaymentIdBasket(ID $basketId, ID $paymentId): BasketDataType
    {
        $basket = $this->accountBasketService->getAuthenticatedCustomerBasket((string) $basketId->val());

        $this->basketInfrastructure->setPayment($basket, (string) $paymentId->val());

        return $basket;
    }

    /**
     * Check if delivery set is available for user basket with concrete id
     */
    public function isDeliveryMethodAvailableForBasket(ID $basketId, ID $deliveryMethodId): bool
    {
        $basket   = $this->accountBasketService->getAuthenticatedCustomerBasket((string) $basketId->val());
        $customer = $this->customerService->customer((string) $basket->getUserId()->val());
        $country  = $this->getBasketDeliveryCountryId($basket);

        $deliveries = $this->basketInfrastructure->getBasketAvailableDeliveryMethods(
            $customer,
            $basket,
            $country
        );

        return array_key_exists((string) $deliveryMethodId->val(), $deliveries);
    }

    /**
     * Update delivery set id for user basket
     * Resets payment id as it may be not available for new delivery set
     */
    public function setDeliveryMethodIdToBasket(ID $basketId, ID $deliveryId): BasketDataType
    {
        $basket = $this->accountBasketService->getAuthenticatedCustomerBasket((string) $basketId->val());

        $this->basketInfrastructure->setDeliveryMethod($basket, (string) $deliveryId->val());

        return $basket;
    }

    /**
     * @return BasketDeliveryMethodDataType[]
     */
    public function getBasketDeliveryMethods(ID $basketId): array
    {
        $basket   = $this->accountBasketService->getAuthenticatedCustomerBasket((string) $basketId->val());
        $customer = $this->customerService->customer((string) $basket->getUserId()->val());
        $country  = $this->getBasketDeliveryCountryId($basket);

        return $this->basketInfrastructure->getBasketAvailableDeliveryMethods(
            $customer,
            $basket,
            $country
        );
    }

    /**
     * @return BasketPayment[]
     */
    public function getBasketPayments(ID $basketId): array
    {
        $basket   = $this->accountBasketService->getAuthenticatedCustomerBasket((string) $basketId->val());
        $customer = $this->customerService->customer((string) $basket->getUserId()->val());
        $country  = $this->getBasketDeliveryCountryId($basket);

        $deliveries = $this->basketInfrastructure->getBasketAvailableDeliveryMethods(
            $customer,
            $basket,
            $country
        );

        $result = [];

        foreach ($deliveries as $delivery) {
            $payments = $delivery->getPaymentTypes();

            $result = array_merge($result, $payments);
        }

        return array_unique($result, SORT_REGULAR);
    }

    /**
     * @throws UnavailableDeliveryMethod
     * @throws UnavailablePayment
     * @throws PlaceOrder
     */
    public function placeOrder(ID $basketId, ?bool $termsAndConditions = null): OrderDataType
    {
        $userBasket = $this->accountBasketService->getAuthenticatedCustomerBasket((string) $basketId->val());

        $this->checkTermsAndConditionsConsent($userBasket, $termsAndConditions);

        /** @var ?DeliveryMethodDataType $deliveryMethod */
        $deliveryMethod = $this->basketRelationService->deliveryMethod($userBasket);

        if ($deliveryMethod === null) {
            throw MissingDeliveryMethod::provideDeliveryMethod();
        }

        if (!$this->isDeliveryMethodAvailableForBasket($userBasket->id(), $deliveryMethod->id())) {
            throw UnavailableDeliveryMethod::byId((string) $deliveryMethod->id()->val());
        }

        /** @var ?PaymentDataType $payment */
        $payment = $this->basketRelationService->payment($userBasket);

        if ($payment === null) {
            throw MissingPayment::providePayment();
        }

        if (!$this->isPaymentMethodAvailableForBasket($userBasket->id(), $payment->getId())) {
            throw UnavailablePayment::byId((string) $payment->getId()->val());
        }

        /** @var CustomerDataType $customer */
        $customer = $this->customerService->customer(
            $this->authenticationService->getUserId()
        );

        return $this->basketInfrastructure->placeOrder(
            $customer,
            $userBasket
        );
    }

    private function deliveryAddressBelongsToUser(string $deliveryAddressId): bool
    {
        $belongs           = false;
        $customerAddresses = $this->deliveryAddressService->customerDeliveryAddresses(new AddressFilterList());

        /** @var DeliveryAddressDataType $address */
        foreach ($customerAddresses as $address) {
            $id      = (string) $address->id()->val();
            $belongs = ($id === $deliveryAddressId);

            if ($belongs) {
                break;
            }
        }

        return $belongs;
    }

    private function getBasketDeliveryCountryId(BasketDataType $basket): CountryDataType
    {
        $countryId = null;

        if ($basketDeliveryAddressId = $basket->getEshopModel()->getFieldData('OEGQL_DELADDRESSID')) {
            $basketDeliveryAddress = $this->deliveryAddressService->getDeliveryAddress($basketDeliveryAddressId);
            $countryId             = (string) $basketDeliveryAddress->countryId()->val();
        }

        // if basket don't have delivery set, use basket user active address country id
        if (!$countryId) {
            $countryId = $this->customerInfrastructure->getUserActiveCountryId(
                (string) $basket->getUserId()->val()
            );
        }

        return $this->countryService->country($countryId);
    }

    private function checkTermsAndConditionsConsent(BasketDataType $basket, ?bool $termsAndConditions): void
    {
        $confirmTermsAndConditions = $this->legacyService->getConfigParam('blConfirmAGB');

        if (($confirmTermsAndConditions && !$termsAndConditions) || $termsAndConditions === false) {
            throw PlaceOrderException::notAcceptedTOS((string) $basket->id()->val());
        }
    }
}
