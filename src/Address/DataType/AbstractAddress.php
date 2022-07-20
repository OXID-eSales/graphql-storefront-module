<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\DataType;

use OxidEsales\Eshop\Core\Model\BaseModel;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Types\ID;

abstract class AbstractAddress
{
    protected const SAL_FIELD_NAME = 'sal';
    protected const FNAME_FIELD_NAME = 'fname';
    protected const LNAME_FIELD_NAME = 'lname';
    protected const COMPANY_FIELD_NAME = 'company';
    protected const INFO_FIELD_NAME = 'addinfo';
    protected const STREET_FIELD_NAME = 'street';
    protected const STREETNR_FIELD_NAME = 'streetnr';
    protected const ZIP_FIELD_NAME = 'zip';
    protected const CITY_FIELD_NAME = 'city';
    protected const PHONE_FIELD_NAME = 'fon';
    protected const FAX_FIELD_NAME = 'fax';
    protected const COUNTRY_FIELD_NAME = 'countryid';
    protected const STATE_FIELD_NAME = 'stateid';

    protected BaseModel $model;
    private string $prefix;

    public function __construct(BaseModel $model, string $prefix)
    {
        $this->model = $model;
        $this->prefix = $prefix;
    }

    /**
     * @Field()
     */
    public function salutation(): string
    {
        return $this->getFieldValue(self::SAL_FIELD_NAME);
    }

    /**
     * @Field()
     */
    public function firstName(): string
    {
        return $this->getFieldValue(self::FNAME_FIELD_NAME);
    }

    /**
     * @Field()
     */
    public function lastName(): string
    {
        return $this->getFieldValue(self::LNAME_FIELD_NAME);
    }

    /**
     * @Field()
     */
    public function company(): string
    {
        return $this->getFieldValue(self::COMPANY_FIELD_NAME);
    }

    /**
     * @Field()
     */
    public function additionalInfo(): string
    {
        return $this->getFieldValue(self::INFO_FIELD_NAME);
    }

    /**
     * @Field()
     */
    public function street(): string
    {
        return $this->getFieldValue(self::STREET_FIELD_NAME);
    }

    /**
     * @Field()
     */
    public function streetNumber(): string
    {
        return $this->getFieldValue(self::STREETNR_FIELD_NAME);
    }

    /**
     * @Field()
     */
    public function zipCode(): string
    {
        return $this->getFieldValue(self::ZIP_FIELD_NAME);
    }

    /**
     * @Field()
     */
    public function city(): string
    {
        return $this->getFieldValue(self::CITY_FIELD_NAME);
    }

    /**
     * @Field()
     */
    public function phone(): string
    {
        return $this->getFieldValue(self::PHONE_FIELD_NAME);
    }

    /**
     * @Field()
     */
    public function fax(): string
    {
        return $this->getFieldValue(self::FAX_FIELD_NAME);
    }

    public function countryId(): ID
    {
        return new ID($this->getFieldValue(self::COUNTRY_FIELD_NAME));
    }

    public function stateId(): ID
    {
        return new ID($this->getFieldValue(self::STATE_FIELD_NAME));
    }

    protected function getFieldValue($field): string
    {
        return (string)$this->model->getRawFieldData($this->prefix.$field);
    }
}
