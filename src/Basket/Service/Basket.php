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
use OxidEsales\GraphQL\Storefront\Address\DataType\AddressFilterList;
use OxidEsales\GraphQL\Storefront\Address\DataType\DeliveryAddress as DeliveryAddressDataType;
use OxidEsales\GraphQL\Storefront\Address\Exception\DeliveryAddressNotFound;
use OxidEsales\GraphQL\Storefront\Address\Service\DeliveryAddress as DeliveryAddressService;
use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket as BasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketCost;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketOwner as BasketOwnerDataType;
use OxidEsales\GraphQL\Storefront\Basket\DataType\PublicBasket as PublicBasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\Event\AfterAddItem;
use OxidEsales\GraphQL\Storefront\Basket\Event\AfterRemoveItem;
use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeAddItem;
use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketDeliveryMethods;
use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketPayments;
use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeBasketRemove;
use OxidEsales\GraphQL\Storefront\Basket\Event\BeforeRemoveItem;
use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketAccessForbidden;
use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketNotFound;
use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Basket as BasketInfrastructure;
use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Repository as BasketRepository;
use OxidEsales\GraphQL\Storefront\Country\DataType\Country as CountryDataType;
use OxidEsales\GraphQL\Storefront\Country\Service\Country as CountryService;
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotFound;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Customer as CustomerInfrastructure;
use OxidEsales\GraphQL\Storefront\Customer\Service\CustomerInterface as CustomerService;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\DataType\BasketDeliveryMethod as BasketDeliveryMethodDataType;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\Exception\UnavailableDeliveryMethod;
use OxidEsales\GraphQL\Storefront\Payment\DataType\BasketPayment;
use OxidEsales\GraphQL\Storefront\Payment\Exception\PaymentValidationFailed;
use OxidEsales\GraphQL\Storefront\Payment\Exception\UnavailablePayment;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Basket as SharedInfrastructure;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Storefront\Voucher\DataType\Voucher as VoucherDataType;
use OxidEsales\GraphQL\Storefront\Voucher\Infrastructure\Repository as VoucherRepository;
use OxidEsales\GraphQL\Storefront\Voucher\Infrastructure\Voucher as VoucherInfrastructure;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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

    /** @var SharedInfrastructure */
    private $sharedInfrastructure;

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

    /** @var CustomerInfrastructure */
    private $customerInfrastructure;

    /** @var DeliveryAddressService */
    private $deliveryAddressService;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    private BasketFinder $basketFinderService;

    public function __construct(
        Repository $repository,
        BasketRepository $basketRepository,
        Authentication $authenticationService,
        Authorization $authorizationService,
        Legacy $legacyService,
        SharedInfrastructure $sharedInfrastructure,
        VoucherInfrastructure $voucherInfrastructure,
        CustomerInfrastructure $customerInfrastructure,
        BasketInfrastructure $basketInfrastructure,
        DeliveryAddressService $deliveryAddressService,
        VoucherRepository $voucherRepository,
        CountryService $countryService,
        CustomerService $customerService,
        EventDispatcherInterface $eventDispatcher,
        BasketFinder $basketFinderService
    ) {
        $this->repository = $repository;
        $this->basketRepository = $basketRepository;
        $this->authenticationService = $authenticationService;
        $this->authorizationService = $authorizationService;
        $this->legacyService = $legacyService;
        $this->sharedInfrastructure = $sharedInfrastructure;
        $this->voucherInfrastructure = $voucherInfrastructure;
        $this->voucherRepository = $voucherRepository;
        $this->basketInfrastructure = $basketInfrastructure;
        $this->deliveryAddressService = $deliveryAddressService;
        $this->customerInfrastructure = $customerInfrastructure;
        $this->countryService = $countryService;
        $this->customerService = $customerService;
        $this->eventDispatcher = $eventDispatcher;
        $this->basketFinderService = $basketFinderService;
    }

    /**
     * @throws BasketNotFound
     * @throws InvalidToken
     */
    public function remove(ID $id): bool
    {
        $this->eventDispatcher->dispatch(
            new BeforeBasketRemove($id),
            BeforeBasketRemove::class
        );

        $basket = $this->basketRepository->getBasketById((string)$id);

        //user can remove only his own baskets unless otherwise authorized
        if (
            $this->authorizationService->isAllowed('DELETE_BASKET')
            || $basket->belongsToUser((string)$this->authenticationService->getUser()->id())
        ) {
            $vouchers = $this->voucherRepository->getBasketVouchers((string)$id);

            /** @var VoucherDataType $voucher */
            foreach ($vouchers as $voucher) {
                $this->voucherInfrastructure->removeVoucher($voucher, $basket);
            }

            return $this->repository->delete($basket->getEshopModel());
        }

        throw BasketAccessForbidden::byAuthenticatedUser();
    }

    /**
     * @throws CustomerNotFound
     */
    public function basketOwner(string $id): BasketOwnerDataType
    {
        $ignoreSubShop = (bool)$this->legacyService->getConfigParam('blMallUsers');

        try {
            /** @var BasketOwnerDataType $customer */
            $customer = $this->repository->getById(
                $id,
                BasketOwnerDataType::class,
                $ignoreSubShop
            );
        } catch (NotFound $e) {
            throw new CustomerNotFound($id);
        }

        return $customer;
    }

    /**
     * @throws InvalidLogin
     * @throws InvalidToken
     */
    public function store(BasketDataType $basket): BasketDataType
    {
        $this->repository->saveModel($basket->getEshopModel());

        return $this->repository->getById(
            $basket->getEshopModel()->getId(),
            BasketDataType::class
        );
    }

    /**
     * @return PublicBasketDataType[]
     */
    public function publicBasketsByOwnerNameOrEmail(string $owner): array
    {
        return $this->basketRepository->publicBasketsByOwnerNameOrEmail(
            $owner
        );
    }

    /**
     * @throws BasketAccessForbidden
     */
    public function makePublic(ID $basketId): BasketDataType
    {
        $basket = $this->basketFinderService->getAuthenticatedCustomerBasket($basketId);

        $this->basketInfrastructure->assignAndSave(
            $basket,
            [
                'oxuserbaskets__oxpublic' => 1
            ]
        );

        return $basket;
    }

    /**
     * @throws BasketAccessForbidden
     */
    public function makePrivate(ID $basketId): BasketDataType
    {
        $basket = $this->basketFinderService->getAuthenticatedCustomerBasket($basketId);

        $this->basketInfrastructure->assignAndSave(
            $basket,
            [
                'oxuserbaskets__oxpublic' => 0
            ]
        );

        return $basket;
    }

    public function basketCost(BasketDataType $basket): BasketCost
    {
        $basketModel = $this->sharedInfrastructure->getCalculatedBasket($basket);

        return new BasketCost($basketModel);
    }

    /**
     * @throws BasketAccessForbidden
     * @throws BasketNotFound
     * @throws DeliveryAddressNotFound
     * @throws InvalidToken
     */
    public function setDeliveryAddress(ID $basketId, ?ID $deliveryAddressId = null): BasketDataType
    {
        $basket = $this->basketFinderService->getAuthenticatedCustomerBasket($basketId);

        if (
            null !== $deliveryAddressId &&
            !$this->deliveryAddressBelongsToUser($deliveryAddressId)
        ) {
            throw new DeliveryAddressNotFound((string)$deliveryAddressId);
        }

        $deliveryAddressId = $deliveryAddressId ? (string)$deliveryAddressId : null;
        $this->basketInfrastructure->assignAndSave(
            $basket,
            [
                'OEGQL_DELADDRESSID' => $deliveryAddressId
            ]
        );

        $this->sharedInfrastructure->getBasket($basket);

        return $basket;
    }

    /**
     * @throws PaymentValidationFailed
     * @throws UnavailablePayment
     */
    public function setPayment(ID $basketId, ID $paymentId): BasketDataType
    {
        if (!$this->isPaymentMethodAvailableForBasket($basketId, $paymentId)) {
            throw UnavailablePayment::byId((string)$paymentId->val());
        }

        return $this->setPaymentIdBasket($basketId, $paymentId);
    }

    /**
     * @throws UnavailableDeliveryMethod
     */
    public function setDeliveryMethod(ID $basketId, ID $deliveryMethodId): BasketDataType
    {
        if (!$this->isDeliveryMethodAvailableForBasket($basketId, $deliveryMethodId)) {
            throw UnavailableDeliveryMethod::byId((string)$deliveryMethodId->val());
        }

        return $this->setDeliveryMethodIdToBasket($basketId, $deliveryMethodId);
    }

    /**
     * Check if payment method is available for user basket with concrete id
     */
    public function isPaymentMethodAvailableForBasket(ID $basketId, ID $paymentId): bool
    {
        $basket = $this->basketFinderService->getAuthenticatedCustomerBasket($basketId);
        $deliveryMethodId = (string)$basket->getDeliveryMethodId()->val();

        if (!$deliveryMethodId) {
            throw PaymentValidationFailed::byDeliveryMethod();
        }

        $deliveries = $this->getAvailableDeliveries($basketId);

        $paymentMethods = isset($deliveries[$deliveryMethodId])
            ? $deliveries[$deliveryMethodId]->getPaymentTypes()
            : [];

        return array_key_exists((string)$paymentId->val(), $paymentMethods);
    }

    /**
     * Updates payment id for the user basket
     */
    public function setPaymentIdBasket(ID $basketId, ID $paymentId): BasketDataType
    {
        $basket = $this->basketFinderService->getAuthenticatedCustomerBasket($basketId);

        $this->basketInfrastructure->assignAndSave(
            $basket,
            [
                'OEGQL_PAYMENTID' => (string)$paymentId->val()
            ]
        );

        return $basket;
    }

    /**
     * Check if delivery set is available for user basket with concrete id
     */
    public function isDeliveryMethodAvailableForBasket(ID $basketId, ID $deliveryMethodId): bool
    {
        $deliveries = $this->getAvailableDeliveries($basketId);

        return array_key_exists((string)$deliveryMethodId->val(), $deliveries);
    }

    /**
     * Update delivery set id for user basket
     * Resets payment id as it may be not available for new delivery set
     */
    public function setDeliveryMethodIdToBasket(ID $basketId, ID $deliveryId): BasketDataType
    {
        $basket = $this->basketFinderService->getAuthenticatedCustomerBasket($basketId);

        // Update delivery method id for user basket
        // Resets payment id as it may be not available for new delivery method
        $this->basketInfrastructure->assignAndSave(
            $basket,
            [
                'OEGQL_DELIVERYMETHODID' => (string)$deliveryId->val(),
                'OEGQL_PAYMENTID' => '',
            ]
        );

        return $basket;
    }

    /**
     * @return BasketDeliveryMethodDataType[]
     */
    public function getBasketDeliveryMethods(ID $basketId): array
    {
        /** @var BeforeBasketDeliveryMethods $event */
        $event = $this->eventDispatcher->dispatch(
            new BeforeBasketDeliveryMethods($basketId),
            BeforeBasketDeliveryMethods::class
        );

        if ($event->getDeliveryMethods() !== null) {
            return $event->getDeliveryMethods();
        }

        return $this->getAvailableDeliveries($basketId);
    }

    /**
     * @return BasketPayment[]
     */
    public function getBasketPayments(ID $basketId): array
    {
        $event = new BeforeBasketPayments($basketId);
        $this->eventDispatcher->dispatch(
            $event,
            BeforeBasketPayments::class
        );

        if ($event->getPayments() !== null) {
            return $event->getPayments();
        }

        $deliveries = $this->getAvailableDeliveries($basketId);

        $result = [];

        foreach ($deliveries as $delivery) {
            $payments = $delivery->getPaymentTypes();

            $result = array_merge($result, $payments);
        }

        return array_unique($result, SORT_REGULAR);
    }

    private function getAvailableDeliveries(ID $basketId): array
    {
        $basket = $this->basketFinderService->getAuthenticatedCustomerBasket($basketId);
        $customer = $this->customerService->customer((string)$basket->getUserId()->val());
        $country = $this->getBasketDeliveryCountryId($basket);

        return $this->basketInfrastructure->getBasketAvailableDeliveryMethods(
            $customer,
            $basket,
            $country
        );
    }

    private function deliveryAddressBelongsToUser(ID $deliveryAddressId): bool
    {
        $belongs = false;
        $customerAddresses = $this->deliveryAddressService->customerDeliveryAddresses(new AddressFilterList());

        /** @var DeliveryAddressDataType $address */
        foreach ($customerAddresses as $address) {
            $id = (string)$address->id()->val();
            $belongs = ($id === (string)$deliveryAddressId);

            if ($belongs) {
                break;
            }
        }

        return $belongs;
    }

    private function getBasketDeliveryCountryId(BasketDataType $basket): CountryDataType
    {
        $countryId = null;

        if ($basket->getDeliveryAddressId()->val()) {
            $basketDeliveryAddress = $this->deliveryAddressService->getDeliveryAddress($basket->getDeliveryAddressId());
            $countryId = (string)$basketDeliveryAddress->countryId()->val();
        }

        // if basket don't have delivery set, use basket user active address country id
        if (!$countryId) {
            $countryId = $this->customerInfrastructure->getUserActiveCountryId(
                (string)$basket->getUserId()->val()
            );
        }

        return $this->countryService->country(new ID($countryId));
    }
}
