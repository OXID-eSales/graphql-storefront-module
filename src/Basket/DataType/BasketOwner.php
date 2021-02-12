<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\DataType;

use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class BasketOwner implements DataType
{
    /** @var EshopUserModel */
    private $owner;

    public function __construct(
        EshopUserModel $owner
    ) {
        $this->owner = $owner;
    }

    public function getEshopModel(): EshopUserModel
    {
        return $this->owner;
    }

    /**
     * @Field()
     */
    public function getFirstName(): string
    {
        return (string) $this->owner->getFieldData('oxfname');
    }

    /**
     * @Field()
     */
    public function getLastName(): string
    {
        return (string) $this->owner->getFieldData('oxlname');
    }

    /**
     * @return class-string
     */
    public static function getModelClass(): string
    {
        return EshopUserModel::class;
    }
}
