<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Service;

use OxidEsales\GraphQL\Base\DataType\StringFilter;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\WishedPrice\DataType\WishedPrice as WishedPriceDataType;
use OxidEsales\GraphQL\Storefront\WishedPrice\DataType\WishedPriceFilterList;
use OxidEsales\GraphQL\Storefront\WishedPrice\Exception\WishedPriceNotFound;
use OxidEsales\GraphQL\Storefront\WishedPrice\Infrastructure\WishedPriceNotification as WishedPriceNotificationInfrastructure;

final class WishedPrice
{
    /** @var Repository */
    private $repository;

    /** @var Authentication */
    private $authenticationService;

    /** @var Authorization */
    private $authorizationService;

    /** @var RelationService */
    private $wishedPriceRelationService;

    /** @var WishedPriceNotificationInfrastructure */
    private $wishedPriceNotificationInfrastructure;

    public function __construct(
        Repository $repository,
        Authentication $authenticationService,
        Authorization $authorizationService,
        RelationService $wishedPriceRelationService,
        WishedPriceNotificationInfrastructure $wishedPriceNotificationInfrastructure
    ) {
        $this->repository                            = $repository;
        $this->authenticationService                 = $authenticationService;
        $this->authorizationService                  = $authorizationService;
        $this->wishedPriceRelationService            = $wishedPriceRelationService;
        $this->wishedPriceNotificationInfrastructure = $wishedPriceNotificationInfrastructure;
    }

    /**
     * @throws InvalidLogin
     * @throws WishedPriceNotFound
     *
     * @return true
     */
    public function delete(string $id): bool
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
    public function wishedPrice(string $id): WishedPriceDataType
    {
        $wishedPrice = $this->getWishedPrice($id);

        /** Check disable wished price flag */
        $product = $this->wishedPriceRelationService->getProduct($wishedPrice);

        if (!$product->wishedPriceEnabled() && !$this->authorizationService->isAllowed('VIEW_WISHED_PRICES')) {
            throw WishedPriceNotFound::byId($id);
        }

        return $wishedPrice;
    }

    /**
     * @throws InvalidToken
     *
     * @return WishedPriceDataType[]
     */
    public function wishedPrices(WishedPriceFilterList $filter): array
    {
        return $this->repository->getByFilter(
            $filter->withUserFilter(
                new StringFilter(
                    $this->authenticationService->getUserId()
                )
            ),
            WishedPriceDataType::class
        );
    }

    public function save(WishedPriceDataType $wishedPrice): bool
    {
        $modelItem = $wishedPrice->getEshopModel();
        $this->wishedPriceNotificationInfrastructure->sendNotification($wishedPrice);

        return $this->repository->saveModel($modelItem);
    }

    /**
     * @throws WishedPriceNotFound
     * @throws InvalidLogin
     */
    private function getWishedPrice(string $id): WishedPriceDataType
    {
        /** Only logged in users can query wished price */
        if (!$this->authenticationService->isLogged()) {
            throw new InvalidLogin('Unauthenticated');
        }

        try {
            /** @var WishedPriceDataType $wishedPrice */
            $wishedPrice = $this->repository->getById(
                $id,
                WishedPriceDataType::class,
                false
            );
        } catch (NotFound $e) {
            throw WishedPriceNotFound::byId($id);
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
        return (string) $wishedPrice->getInquirerId() === (string) $this->authenticationService->getUserId();
    }
}
