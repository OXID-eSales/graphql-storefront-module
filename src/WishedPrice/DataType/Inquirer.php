<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\DataType;

use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class Inquirer implements DataType
{
    /** @var EshopUserModel */
    private $inquirer;

    public function __construct(EshopUserModel $inquirer)
    {
        $this->inquirer = $inquirer;
    }

    public function getEshopModel(): EshopUserModel
    {
        return $this->inquirer;
    }

    /**
     * @Field()
     */
    public function getFirstName(): string
    {
        return (string) $this->inquirer->getRawFieldData('oxfname');
    }

    public static function getModelClass(): string
    {
        return EshopUserModel::class;
    }
}
