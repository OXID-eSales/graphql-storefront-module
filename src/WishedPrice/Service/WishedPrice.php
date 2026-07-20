<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Service;

use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\RepositoryInterface;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product as ProductDataType;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Storefront\WishedPrice\DataType\WishedPrice as WishedPriceDataType;
use OxidEsales\GraphQL\Storefront\WishedPrice\DataType\WishedPriceFilterList;
use OxidEsales\GraphQL\Storefront\WishedPrice\Exception\WishedPriceNotFound;
use OxidEsales\GraphQL\Storefront\WishedPrice\Infrastructure\WishedPriceFactoryInterface;
use OxidEsales\GraphQL\Storefront\WishedPrice\Infrastructure\WishedPriceNotification as WishedPriceNotificationInfrastructure; // phpcs:ignore
use OxidEsales\GraphQL\Storefront\WishedPrice\Input\WishedPriceInputInterface;
use TheCodingMachine\GraphQLite\Types\ID;

final class WishedPrice
{
    /** @var RepositoryInterface */
    private $repository;

    /** @var Authentication */
    private $authenticationService;

    /** @var Authorization */
    private $authorizationService;

    /** @var RelationService */
    private $wishedPriceRelationService;

    /** @var WishedPriceNotificationInfrastructure */
    private $wishedPriceNotificationInfrastructure;

    /** @var WishedPriceInputValidatorInterface */
    private $wishedPriceInputValidator;

    /** @var WishedPriceFactoryInterface */
    private $wishedPriceFactory;

    public function __construct(
        RepositoryInterface $repository,
        Authentication $authenticationService,
        Authorization $authorizationService,
        RelationService $wishedPriceRelationService,
        WishedPriceNotificationInfrastructure $wishedPriceNotificationInfrastructure,
        WishedPriceInputValidatorInterface $wishedPriceInputValidator,
        WishedPriceFactoryInterface $wishedPriceFactory
    ) {
        $this->repository = $repository;
        $this->authenticationService = $authenticationService;
        $this->authorizationService = $authorizationService;
        $this->wishedPriceRelationService = $wishedPriceRelationService;
        $this->wishedPriceNotificationInfrastructure = $wishedPriceNotificationInfrastructure;
        $this->wishedPriceInputValidator = $wishedPriceInputValidator;
        $this->wishedPriceFactory = $wishedPriceFactory;
    }

    /**
     * @throws ProductNotFound
     */
    public function set(WishedPriceInputInterface $wishedPrice): WishedPriceDataType
    {
        $this->wishedPriceInputValidator->validatePrice($wishedPrice->getPrice());

        $this->assertProductWishedPriceIsPossible($wishedPrice->getProductId());

        $user = $this->authenticationService->getUser();

        $wishedPriceDataType = $this->wishedPriceFactory->createWishedPrice(
            (string)$user->id(),
            $user->email(),
            $wishedPrice->getProductId(),
            $wishedPrice->getCurrencyName(),
            $wishedPrice->getPrice()
        );

        return $this->save($wishedPriceDataType);
    }

    /**
     * @throws InvalidLogin
     * @throws WishedPriceNotFound
     *
     * @return true
     */
    public function delete(ID $id): bool
    {
        $wishedPrice = $this->getWishedPrice($id);

        //we got this far, we have a user
        //user can delete only its own wished price, admin can delete any wished price
        if (
            $this->authorizationService->isAllowed('DELETE_WISHED_PRICE')
            || $this->isSameUser($wishedPrice)
        ) {
            return $this->repository->delete($wishedPrice->getEshopModel());
        }

        throw new InvalidLogin('Unauthorized');
    }

    /**
     * @throws WishedPriceNotFound
     */
    public function wishedPrice(ID $id): WishedPriceDataType
    {
        $wishedPrice = $this->getWishedPrice($id);

        /** Check disable wished price flag */
        $product = $this->wishedPriceRelationService->getProduct($wishedPrice);

        if (!$product->wishedPriceEnabled() && !$this->authorizationService->isAllowed('VIEW_WISHED_PRICES')) {
            throw new WishedPriceNotFound((string)$id);
        }

        return $wishedPrice;
    }

    /**
     * @return WishedPriceDataType[]
     * @throws InvalidToken
     *
     */
    public function wishedPrices(WishedPriceFilterList $filter): array
    {
        return $this->repository->getByFilter(
            $filter->withUserFilter(
                new IDFilter(
                    $this->authenticationService->getUser()->id()
                )
            ),
            WishedPriceDataType::class
        );
    }

    private function save(WishedPriceDataType $wishedPrice): WishedPriceDataType
    {
        $modelItem = $wishedPrice->getEshopModel();
        $this->wishedPriceNotificationInfrastructure->sendNotification($wishedPrice);

        $this->repository->saveModel($modelItem);

        return $this->repository->getById(
            $modelItem->getId(),
            WishedPriceDataType::class
        );
    }

    /**
     * @return true
     * @throws ProductNotFound
     */
    private function assertProductWishedPriceIsPossible(ID $productId): bool
    {
        $id = (string)$productId->val();

        try {
            /** @var ProductDataType $product */
            $product = $this->repository->getById($id, ProductDataType::class);
        } catch (NotFound $e) {
            throw new ProductNotFound($id);
        }

        // Throw 404 if product has wished prices disabled
        if (!$product->getEshopModel()->isPriceAlarm()) {
            throw new ProductNotFound($id);
        }

        return true;
    }

    /**
     * @throws WishedPriceNotFound
     * @throws InvalidLogin
     */
    private function getWishedPrice(ID $id): WishedPriceDataType
    {
        /** Only logged in users can query wished price */
        if (!$this->authenticationService->isLogged()) {
            throw new InvalidLogin('Unauthenticated');
        }

        try {
            /** @var WishedPriceDataType $wishedPrice */
            $wishedPrice = $this->repository->getById(
                (string)$id,
                WishedPriceDataType::class,
                false
            );
        } catch (NotFound $e) {
            throw new WishedPriceNotFound((string)$id);
        }

        /** If the logged in user is authorized return the wished price */
        if ($this->authorizationService->isAllowed('VIEW_WISHED_PRICES')) {
            return $wishedPrice;
        }

        /** A user can query only its own wished price */
        if (!$this->isSameUser($wishedPrice)) {
            throw new InvalidLogin('Unauthorized');
        }

        return $wishedPrice;
    }

    private function isSameUser(WishedPriceDataType $wishedPrice): bool
    {
        return (string)$wishedPrice->getInquirerId() === (string)$this->authenticationService->getUser()->id();
    }
}
