<?php

namespace OxidEsales\GraphQL\Storefront\Customer\Service;

use DateTimeInterface;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotDeletable;
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotFoundByUpdateId;

interface CustomerInterface
{
    /**
     * @throws InvalidLogin
     * @throws CustomerNotFoundByUpdateId
     */
    public function customer(string $id): CustomerDataType;

    public function create(CustomerDataType $customer): CustomerDataType;

    public function changeEmail(string $email): CustomerDataType;

    public function changeBirthdate(DateTimeInterface $birthdate): CustomerDataType;

    /**
     * @throws CustomerNotDeletable
     */
    public function deleteCustomer(): bool;

    public function fetchCustomer(string $id): CustomerDataType;
}
