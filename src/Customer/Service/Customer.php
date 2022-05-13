<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Service;

use DateTimeInterface;
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
use OxidEsales\GraphQL\Storefront\Shared\Service\Authorization;

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

    /** @var Authorization */
    private $authorizationService;

    public function __construct(
        Repository $repository,
        CustomerRepository $customerRepository,
        Authentication $authenticationService,
        Legacy $legacyService,
        Authorization $authorizationService
    ) {
        $this->repository = $repository;
        $this->customerRepository = $customerRepository;
        $this->authenticationService = $authenticationService;
        $this->legacyService = $legacyService;
        $this->authorizationService = $authorizationService;
    }

    /**
     * @throws InvalidLogin
     * @throws CustomerNotFound
     */
    public function customer(string $id): CustomerDataType
    {
        if (
            (string)$id !== (string)$this->authenticationService->getUser()->id() &&
            !$this->authorizationService->isAllowed('VIEW_ALL_CUSTOMERS')
        ) {
            throw new InvalidLogin('Unauthorized');
        }

        return $this->fetchCustomer($id);
    }

    public function create(CustomerDataType $customer): CustomerDataType
    {
        return $this->customerRepository->createUser($customer->getEshopModel());
    }

    public function changeEmail(string $email): CustomerDataType
    {
        if (!((string)$id = (string)$this->authenticationService->getUser()->id())) {
            throw new InvalidLogin('Unauthorized');
        }

        if (!strlen($email)) {
            throw InvalidEmail::byEmptyString();
        }

        if (!$this->legacyService->isValidEmail($email)) {
            throw InvalidEmail::byString($email);
        }

        $customer = $this->fetchCustomer($id);

        if ($customer->getEshopModel()->checkIfEmailExists($email)) {
            throw CustomerExists::byEmail($email);
        }

        return $this->updateCustomer($customer, [
            'OXUSERNAME' => $email,
        ]);
    }

    public function changeBirthdate(DateTimeInterface $birthdate): CustomerDataType
    {
        if (!((string)$id = (string)$this->authenticationService->getUser()->id())) {
            throw new InvalidLogin('Unauthorized');
        }

        $customer = $this->fetchCustomer($id);

        return $this->updateCustomer($customer, [
            'OXBIRTHDATE' => $birthdate->format('Y-m-d 00:00:00'),
        ]);
    }

    /**
     * @throws CustomerNotDeletable
     */
    public function deleteCustomer(): bool
    {
        if (!((string)$id = (string)$this->authenticationService->getUser()->id())) {
            throw new InvalidLogin('Unauthorized');
        }

        if (!(bool)$this->legacyService->getConfigParam('blAllowUsersToDeleteTheirAccount')) {
            throw CustomerNotDeletable::notEnabledByAdmin();
        }

        $customerModel = $this->fetchCustomer($id)->getEshopModel();

        if ((bool)$customerModel->isMallAdmin()) {
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

    public function fetchCustomer(string $id): CustomerDataType
    {
        $ignoreSubShop = (bool)$this->legacyService->getConfigParam('blMallUsers');

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

    private function updateCustomer(CustomerDataType $customer, array $data = []): CustomerDataType
    {
        $customerModel = $customer->getEshopModel();

        $customerModel->assign($data);
        $customerModel->save();

        return $customer;
    }
}
