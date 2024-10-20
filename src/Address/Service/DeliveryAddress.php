<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Service;

use OxidEsales\GraphQL\Base\DataType\Filter\StringFilter;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Storefront\Address\DataType\AddressFilterList;
use OxidEsales\GraphQL\Storefront\Address\DataType\DeliveryAddress as DeliveryAddressDataType;
use OxidEsales\GraphQL\Storefront\Address\Exception\DeliveryAddressNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Base\Service\Authorization;
use TheCodingMachine\GraphQLite\Types\ID;

final class DeliveryAddress
{
    /** @var Repository */
    private $repository;

    /** @var Authentication */
    private $authenticationService;

    /** @var Authorization */
    private $authorizationService;

    public function __construct(
        Repository $repository,
        Authentication $authenticationService,
        Authorization $authorizationService
    ) {
        $this->repository = $repository;
        $this->authenticationService = $authenticationService;
        $this->authorizationService = $authorizationService;
    }

    /**
     * @return DeliveryAddressDataType[]
     */
    public function customerDeliveryAddresses(AddressFilterList $filterList): array
    {
        return $this->repository->getByFilter(
            $filterList->withUserFilter(
                new StringFilter(
                    (string)$this->authenticationService->getUser()->id()
                )
            ),
            DeliveryAddressDataType::class
        );
    }

    /**
     * @throws InvalidLogin
     * @throws DeliveryAddressNotFound
     */
    public function delete(ID $id): bool
    {
        $deliveryAddress = $this->getDeliveryAddress($id);

        //we got this far, we have a user
        //user can delete only its own delivery address, admin can delete any delivery address
        if (
            $this->authorizationService->isAllowed('DELETE_DELIVERY_ADDRESS')
            || $this->isSameUser($deliveryAddress)
        ) {
            return $this->repository->delete($deliveryAddress->getEshopModel());
        }

        throw new InvalidLogin('Unauthorized');
    }

    public function store(DeliveryAddressDataType $address): DeliveryAddressDataType
    {
        $this->repository->saveModel(
            $address->getEshopModel()
        );

        return $this->repository->getById(
            $address->getEshopModel()->getId(),
            DeliveryAddressDataType::class
        );
    }

    /**
     * @throws DeliveryAddressNotFound
     * @throws InvalidLogin
     * @throws InvalidToken
     */
    public function getDeliveryAddress(ID $id): DeliveryAddressDataType
    {
        $id = (string)$id->val();

        /** Only logged in users can query delivery addresses */
        if (!$this->authenticationService->isLogged()) {
            throw new InvalidLogin('Unauthenticated');
        }

        try {
            /** @var DeliveryAddressDataType $deliveryAddress */
            $deliveryAddress = $this->repository->getById(
                $id,
                DeliveryAddressDataType::class,
                false
            );
        } catch (NotFound $e) {
            throw new DeliveryAddressNotFound($id);
        }

        return $deliveryAddress;
    }

    private function isSameUser(DeliveryAddressDataType $deliveryAddress): bool
    {
        return (string)$deliveryAddress->userId() === (string)$this->authenticationService->getUser()->id();
    }
}
