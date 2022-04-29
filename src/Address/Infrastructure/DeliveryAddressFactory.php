<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Infrastructure;

use OxidEsales\Eshop\Application\Model\Address as EshopAddressModel;
use OxidEsales\Eshop\Application\Model\Country as EshopCountryModel;
use OxidEsales\Eshop\Application\Model\RequiredAddressFields;
use OxidEsales\Eshop\Application\Model\RequiredFieldsValidator;
use OxidEsales\Eshop\Application\Model\State as EshopStateModel;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\GraphQL\Storefront\Address\DataType\DeliveryAddress as DeliveryAddressDataType;
use OxidEsales\GraphQL\Storefront\Address\Exception\AddressMissingFields;
use TheCodingMachine\GraphQLite\Types\ID;

final class DeliveryAddressFactory
{
    public function createValidAddressType(
        string $userid,
        ?string $salutation = null,
        ?string $firstname = null,
        ?string $lastname = null,
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
        /** @var EshopAddressModel */
        $address = oxNew(EshopAddressModel::class);
        $address->assign([
            'oxsal'       => $salutation,
            'oxuserid'    => $userid,
            'oxfname'     => $firstname,
            'oxlname'     => $lastname,
            'oxcompany'   => $company,
            'oxaddinfo'   => $additionalInfo,
            'oxstreet'    => $street,
            'oxstreetnr'  => $streetNumber,
            'oxzip'       => $zipCode,
            'oxcity'      => $city,
            'oxcountryid' => (string) $countryId,
            'oxstateid'   => (string) $stateId,
            'oxfon'       => $phone,
            'oxfax'       => $fax,
        ]);

        /** @var RequiredFieldsValidator */
        $validator = oxNew(RequiredFieldsValidator::class);
        /** @var RequiredAddressFields */
        $requiredAddressFields = oxNew(RequiredAddressFields::class);
        $requiredFields        = $requiredAddressFields->getDeliveryFields();
        $validator->setRequiredFields(
            $requiredFields
        );

        $externalFields = [
            'oxcountryid' => [
                'class' => EshopCountryModel::class,
                'id'    => (string) $countryId,
            ],
            'oxstateid' => [
                'class' => EshopStateModel::class,
                'id'    => (string) $stateId,
            ],
        ];

        foreach ($externalFields as $field => ['class' => $class, 'id' => $id]) {
            if (in_array('oxaddress__' . $field, $requiredFields, true)) {
                /** @var BaseModel */
                $object = oxNew($class);

                if (!$object->load($id)) {
                    $address->assign([
                        $field => null,
                    ]);
                }
            }
        }

        if (!$validator->validateFields($address)) {
            $invalidFields = array_map(
                function ($v) {
                    return str_replace('oxaddress__ox', '', $v);
                },
                $validator->getInvalidFields()
            );

            throw new AddressMissingFields('delivery', $invalidFields);
        }

        return new DeliveryAddressDataType(
            $address
        );
    }
}
