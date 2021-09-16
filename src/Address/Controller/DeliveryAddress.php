<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Controller;

use OxidEsales\GraphQL\Storefront\Address\DataType\AddressFilterList;
use OxidEsales\GraphQL\Storefront\Address\DataType\DeliveryAddress as DeliveryAddressDataType;
use OxidEsales\GraphQL\Storefront\Address\Service\DeliveryAddress as DeliveryAddressService;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\HideIfUnauthorized;
use TheCodingMachine\GraphQLite\Types\ID;

final class DeliveryAddress
{
    /** @var DeliveryAddressService */
    private $deliveryAddressService;

    public function __construct(
        DeliveryAddressService $deliveryAddressService
    ) {
        $this->deliveryAddressService = $deliveryAddressService;
    }

    /**
     * @Query()
     * @Logged()
     * @HideIfUnauthorized()
     *
     * @return DeliveryAddressDataType[]
     */
    public function customerDeliveryAddresses(): array
    {
        return $this->deliveryAddressService->customerDeliveryAddresses(
            new AddressFilterList()
        );
    }

    /**
     * @Mutation()
     * @Logged()
     * @HideIfUnauthorized()
     */
    public function customerDeliveryAddressDelete(ID $deliveryAddressId): bool
    {
        return $this->deliveryAddressService->delete($deliveryAddressId);
    }

    /**
     * @Mutation()
     * @Logged()
     * @HideIfUnauthorized()
     */
    public function customerDeliveryAddressAdd(DeliveryAddressDataType $deliveryAddress): DeliveryAddressDataType
    {
        $this->deliveryAddressService->store($deliveryAddress);

        return $deliveryAddress;
    }
}
