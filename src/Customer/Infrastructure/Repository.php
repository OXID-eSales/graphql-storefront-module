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
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotFoundByUpdateId;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\OxNewFactoryInterface;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository as SharedRepository;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\RepositoryInterface as SharedRepositoryInterface;

final class Repository implements RepositoryInterface
{
    /** @var SharedRepository */
    private $repository;

    public function __construct(
        SharedRepositoryInterface $repository,
        OxNewFactoryInterface $oxNewFactory
    ) {
        $this->repository = $repository;
        $this->oxNewFactory = $oxNewFactory;
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
            throw new CustomerNotFound($user->getId());
        }

        //Todo: Parameter if private sales is active or not, will be implemented in OXDEV-5273
        $user->sendRegistrationEmail();

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

    public function saveNewPasswordForCustomer(EshopUserModel $customer, string $newPassword): bool
    {
        $customer->setPassword($newPassword);
        $customer->setUpdateKey(true);
        return $this->repository->saveModel($customer);
    }

    /**
     * @throws CustomerNotFoundByUpdateId
     */
    public function getCustomerByPasswordUpdateId(string $passwordUpdateId): EshopUserModel
    {
        $user = $this->oxNewFactory->getModel(EshopUserModel::class);
        $user = $user->loadUserByUpdateId($passwordUpdateId);
        if (!$user instanceof EshopUserModel || !$user->isLoaded()) {
            throw new CustomerNotFoundByUpdateId($passwordUpdateId);
        }

        return $user;
    }
}
