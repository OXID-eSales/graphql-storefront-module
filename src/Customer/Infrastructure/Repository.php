<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Infrastructure;

use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\GraphQL\Storefront\Address\DataType\DeliveryAddress;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository as SharedRepository;

final class Repository
{
    /** @var SharedRepository */
    private $repository;

    public function __construct(
        SharedRepository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @throws CustomerNotFound
     */
    public function createUser(EshopUserModel $user): CustomerDataType
    {
        if (!$user->exists()) {
            $this->repository->saveModel($user);
            //A newly created user will be assigned to oxidnotyetordered user groups,
            //same as in \OxidEsales\EshopCommunity\Application\Component\UserComponent::createUser()
            $user->addToGroup('oxidnotyetordered');
        }

        if (!$user->load($user->getId())) {
            throw CustomerNotFound::byId($user->getId());
        }

        return new CustomerDataType($user);
    }

    /**
     * @return DeliveryAddress[]
     */
    public function addresses(CustomerDataType $customer): array
    {
        $addresses = [];
        $addressList = $customer->getEshopModel()
            ->getUserAddresses();

        foreach ($addressList as $address) {
            $addresses[] = new DeliveryAddress($address);
        }

        return $addresses;
    }

    public function checkEmailExists(string $email): bool
    {
        /** @var EshopUserModel $customerModel */
        $customerModel = oxNew(CustomerDataType::getModelClass());

        return (bool)$customerModel->checkIfEmailExists($email);
    }
}
