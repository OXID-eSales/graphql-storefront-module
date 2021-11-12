<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Infrastructure;

use OxidEsales\Eshop\Application\Model\Country as EshopCountryModel;
use OxidEsales\Eshop\Application\Model\RequiredAddressFields;
use OxidEsales\Eshop\Application\Model\RequiredFieldsValidator;
use OxidEsales\Eshop\Application\Model\State as EshopStateModel;
use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\GraphQL\Storefront\Address\DataType\InvoiceAddress as InvoiceAddressDataType;
use OxidEsales\GraphQL\Storefront\Address\Exception\InvoiceAddressMissingFields;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;
use TheCodingMachine\GraphQLite\Types\ID;

final class InvoiceAddressFactory
{
    public function createValidInvoiceAddressType(
        CustomerDataType $customerDataType,
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
        ?string $vatID = null,
        ?string $phone = null,
        ?string $mobile = null,
        ?string $fax = null
    ): InvoiceAddressDataType {
        /** @var EshopUserModel $customer */
        $customer = $customerDataType->getEshopModel();

        $customer->assign(
            [
                'oxsal'       => $salutation,
                'oxfname'     => $firstName,
                'oxlname'     => $lastName,
                'oxcompany'   => $company ?: $customer->getFieldData('oxcompany'),
                'oxaddinfo'   => $additionalInfo ?: $customer->getFieldData('oxaddinfo'),
                'oxstreet'    => $street,
                'oxstreetnr'  => $streetNumber,
                'oxzip'       => $zipCode,
                'oxcity'      => $city,
                'oxcountryid' => (string) $countryId,
                'oxstateid'   => (string) $stateId,
                'oxustid'     => $vatID ?: $customer->getFieldData('oxustid'),
                'oxprivphone' => $phone ?: $customer->getFieldData('oxprivphone'),
                'oxmobfone'   => $mobile ?: $customer->getFieldData('oxmobfone'),
                'oxfax'       => $fax ?: $customer->getFieldData('oxfax'),
            ]
        );

        /** @var RequiredFieldsValidator */
        $validator = oxNew(RequiredFieldsValidator::class);

        /** @var RequiredAddressFields */
        $requiredAddressFields = oxNew(RequiredAddressFields::class);
        $requiredFields        = $requiredAddressFields->getBillingFields();
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
                    $customer->assign([
                        $field => null,
                    ]);
                }
            }
        }

        if (!$validator->validateFields($customer)) {
            $invalidFields = array_map(
                function ($v) {
                    return str_replace('oxuser__ox', '', $v);
                },
                $validator->getInvalidFields()
            );

            throw InvoiceAddressMissingFields::byFields($invalidFields);
        }

        return new InvoiceAddressDataType($customer);
    }
}
