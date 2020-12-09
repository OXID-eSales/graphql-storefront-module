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
use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket as BasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketCost;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketOwner as BasketOwnerDataType;
use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketAccessForbidden;
use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketNotFound;
use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Basket as BasketInfraService;
use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Repository as BasketRepository;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotFound;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Basket as SharedInfrastructure;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Voucher\DataType\Voucher as VoucherDataType;
use OxidEsales\GraphQL\Storefront\Voucher\Infrastructure\Repository as VoucherRepository;
use OxidEsales\GraphQL\Storefront\Voucher\Infrastructure\Voucher as VoucherInfrastructure;

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
        VoucherRepository $voucherRepository
    ) {
        $this->repository            = $repository;
        $this->basketRepository      = $basketRepository;
        $this->authenticationService = $authenticationService;
        $this->authorizationService  = $authorizationService;
        $this->legacyService         = $legacyService;
        $this->basketInfraService    = $basketInfraService;
        $this->productService        = $productService;
        $this->sharedInfrastructure  = $sharedInfrastructure;
        $this->basketVoucherService  = $basketVoucherService;
        $this->voucherInfrastructure = $voucherInfrastructure;
        $this->voucherRepository     = $voucherRepository;
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
}
