<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Service;

use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Storefront\Address\DataType\DeliveryAddress as DeliveryAddressDataType;
use OxidEsales\GraphQL\Storefront\Address\Infrastructure\DeliveryAddressFactory;
use TheCodingMachine\GraphQLite\Annotations\Factory;
use TheCodingMachine\GraphQLite\Types\ID;

final class DeliveryAddressInput
{
    /** @var DeliveryAddressFactory */
    private $deliveryAddressFactory;

    /** @var Authentication */
    private $authenticationService;

    public function __construct(
        DeliveryAddressFactory $deliveryAddressFactory,
        Authentication $authenticationService
    ) {
        $this->deliveryAddressFactory = $deliveryAddressFactory;
        $this->authenticationService  = $authenticationService;
    }

    /**
     * @Factory
     */
    public function fromUserInput(
        ?string $salutation = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $company = null,
        ?string $additionalInfo = null,
        ?string $street = null,
        ?string $streetNumber = null,
        ?string $zipCode = null,
        ?string $city = null,
        ?ID $countryId = null,
        ?ID $stateId = null,
        ?string $phone = null,
        ?string $fax = null
    ): DeliveryAddressDataType {
        return $this->deliveryAddressFactory->createValidAddressType(
            $this->authenticationService->getUserId(),
            $salutation,
            $firstName,
            $lastName,
            $company,
            $additionalInfo,
            $street,
            $streetNumber,
            $zipCode,
            $city,
            $countryId,
            $stateId,
            $phone,
            $fax
        );
    }
}
