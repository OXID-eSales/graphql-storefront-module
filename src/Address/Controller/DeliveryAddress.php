<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Controller;

use OxidEsales\GraphQL\Storefront\Address\DataType\AddressFilterList;
use OxidEsales\GraphQL\Storefront\Address\DataType\DeliveryAddress as DeliveryAddressDataType;
use OxidEsales\GraphQL\Storefront\Address\Input\DeliveryAddressInputInterface;
use OxidEsales\GraphQL\Storefront\Address\Service\DeliveryAddress as DeliveryAddressService;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\UseInputType;
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
     *
     * @return DeliveryAddressDataType[]
     */
    #[Query]
    #[Logged]
    public function customerDeliveryAddresses(): array
    {
        return $this->deliveryAddressService->customerDeliveryAddresses(
            new AddressFilterList()
        );
    }

    #[Mutation]
    #[Logged]
    public function customerDeliveryAddressDelete(ID $deliveryAddressId): bool
    {
        return $this->deliveryAddressService->delete($deliveryAddressId);
    }

    #[Mutation]
    #[Logged]
    public function customerDeliveryAddressAdd(
        #[UseInputType("DeliveryAddressInput")]
        DeliveryAddressInputInterface $deliveryAddress
    ): DeliveryAddressDataType {
        return $this->deliveryAddressService->store($deliveryAddress);
    }
}
