<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Controller;

use DateTimeInterface;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;
use OxidEsales\GraphQL\Storefront\Customer\Service\Customer as CustomerService;
use TheCodingMachine\GraphQLite\Annotations\HideIfUnauthorized;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class Customer
{
    /** @var CustomerService */
    private $customerService;

    /** @var Authentication */
    private $authenticationService;

    public function __construct(
        CustomerService $customerService,
        Authentication $authenticationService
    ) {
        $this->customerService = $customerService;
        $this->authenticationService = $authenticationService;
    }

    /**
     * @Query()
     * @Logged()
     * @HideIfUnauthorized()
     */
    public function customer(): CustomerDataType
    {
        return $this->customerService->customer(
            (string)$this->authenticationService->getUser()->id()
        );
    }

    /**
     * @Mutation()
     */
    public function customerRegister(CustomerDataType $customer): CustomerDataType
    {
        return $this->customerService->create($customer);
    }

    /**
     * @Mutation()
     * @Logged()
     * @HideIfUnauthorized()
     */
    public function customerEmailUpdate(string $email): CustomerDataType
    {
        return $this->customerService->changeEmail($email);
    }

    /**
     * @Mutation()
     * @Logged()
     * @HideIfUnauthorized()
     */
    public function customerBirthdateUpdate(DateTimeInterface $birthdate): CustomerDataType
    {
        return $this->customerService->changeBirthdate($birthdate);
    }

    /**
     * @Mutation()
     * @Logged()
     * @HideIfUnauthorized()
     */
    public function customerDelete(): bool
    {
        return $this->customerService->deleteCustomer();
    }
}
