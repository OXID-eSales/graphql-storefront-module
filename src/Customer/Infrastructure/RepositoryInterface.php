<?php

namespace OxidEsales\GraphQL\Storefront\Customer\Infrastructure;

use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\GraphQL\Storefront\Address\DataType\DeliveryAddress;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotFoundByUpdateHash;

interface RepositoryInterface
{
    /**
     * @throws CustomerNotFoundByUpdateHash
     */
    public function createUser(EshopUserModel $user): CustomerDataType;

    /**
     * @return DeliveryAddress[]
     */
    public function addresses(CustomerDataType $customer): array;

    public function checkEmailExists(string $email): bool;

    public function saveNewPasswordForCustomer(EshopUserModel $customer, string $newPassword): bool;

    public function getCustomerByPasswordUpdateHash(string $passwordUpdateId): EshopUserModel;
}
