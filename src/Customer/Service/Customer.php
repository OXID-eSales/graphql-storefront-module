<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Service;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerExists;
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotDeletable;
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotFound;
use OxidEsales\GraphQL\Storefront\Customer\Exception\InvalidEmail;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Repository as CustomerRepository;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;

final class Customer
{
    /** @var Repository */
    private $repository;

    /** @var CustomerRepository */
    private $customerRepository;

    /** @var Authentication */
    private $authenticationService;

    /** @var Legacy */
    private $legacyService;

    public function __construct(
        Repository $repository,
        CustomerRepository $customerRepository,
        Authentication $authenticationService,
        Legacy $legacyService
    ) {
        $this->repository            = $repository;
        $this->customerRepository    = $customerRepository;
        $this->authenticationService = $authenticationService;
        $this->legacyService         = $legacyService;
    }

    /**
     * @throws InvalidLogin
     * @throws CustomerNotFound
     */
    public function customer(string $id): CustomerDataType
    {
        if ((string) $id !== (string) $this->authenticationService->getUserId()) {
            throw new InvalidLogin('Unauthorized');
        }

        if ($this->authenticationService->isUserAnonymous() === true) {
            //todo: move to infrastructure
            $user = oxNew(User::class);
            $user->setId($id);

            return new CustomerDataType($user);
        }

        return $this->fetchCustomer($id);
    }

    public function create(CustomerDataType $customer): CustomerDataType
    {
        return $this->customerRepository->createUser($customer->getEshopModel());
    }

    public function changeEmail(string $email): CustomerDataType
    {
        if (!((string) $id = $this->authenticationService->getUserId())) {
            throw new InvalidLogin('Unauthorized');
        }

        if (!strlen($email)) {
            throw InvalidEmail::byEmptyString();
        }

        if (!$this->legacyService->isValidEmail($email)) {
            throw InvalidEmail::byString($email);
        }

        $customer = $this->fetchCustomer($id);

        if ($customer->getEshopModel()->checkIfEmailExists($email) == true) {
            throw CustomerExists::byEmail($email);
        }

        return $this->updateCustomer($customer, [
            'OXUSERNAME' => $email,
        ]);
    }

    public function changeBirthdate(DateTimeInterface $birthdate): CustomerDataType
    {
        if (!((string) $id = $this->authenticationService->getUserId())) {
            throw new InvalidLogin('Unauthorized');
        }

        $customer = $this->fetchCustomer($id);

        return $this->updateCustomer($customer, [
            'OXBIRTHDATE' => $birthdate->format('Y-m-d 00:00:00'),
        ]);
    }

    /**
     * @throws CustomerNotFound
     */
    public function basketOwner(string $id): CustomerDataType
    {
        $ignoreSubShop = (bool) $this->legacyService->getConfigParam('blMallUsers');

        try {
            /** @var CustomerDataType $customer */
            $customer = $this->repository->getById(
                $id,
                CustomerDataType::class,
                $ignoreSubShop
            );
        } catch (NotFound $e) {
            throw CustomerNotFound::byId($id);
        }

        return $customer;
    }

    /**
     * @throws CustomerNotDeletable
     */
    public function deleteCustomer(): bool
    {
        if (!((string) $id = $this->authenticationService->getUserId())) {
            throw new InvalidLogin('Unauthorized');
        }

        if (!(bool) $this->legacyService->getConfigParam('blAllowUsersToDeleteTheirAccount')) {
            throw CustomerNotDeletable::notEnabledByAdmin();
        }

        $customerModel = $this->fetchCustomer($id)->getEshopModel();

        if ((bool) $customerModel->isMallAdmin()) {
            throw CustomerNotDeletable::whileMallAdmin();
        }

        /**
         * Setting derived to false allows mall users to delete their account in a shop
         * that's different from the one the account was originally created in.
         */
        if ($this->legacyService->getConfigParam('blMallUsers')) {
            $customerModel->setIsDerived(false);
        }

        if (!$customerModel->delete()) {
            throw CustomerNotDeletable::byModel();
        }

        return true;
    }

    private function fetchCustomer(string $id): CustomerDataType
    {
        $ignoreSubshop = (bool) $this->legacyService->getConfigParam('blMallUsers');

        try {
            /** @var CustomerDataType $customer */
            $customer = $this->repository->getById(
                $id,
                CustomerDataType::class,
                $ignoreSubshop
            );
        } catch (NotFound $e) {
            throw CustomerNotFound::byId($id);
        }

        return $customer;
    }

    private function updateCustomer(CustomerDataType $customer, array $data = []): CustomerDataType
    {
        $customerModel = $customer->getEshopModel();

        $customerModel->assign($data);
        $customerModel->save();

        return $customer;
    }
}
