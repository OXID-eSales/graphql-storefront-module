<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Review\DataType;

use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class Reviewer implements ShopModelAwareInterface
{
    /** @var EshopUserModel */
    private $reviewer;

    public function __construct(EshopUserModel $reviewer)
    {
        $this->reviewer = $reviewer;
    }

    public function getEshopModel(): EshopUserModel
    {
        return $this->reviewer;
    }

    /**
     * @Field()
     */
    public function getFirstName(): string
    {
        return (string) $this->reviewer->getFieldData('oxfname');
    }

    public static function getModelClass(): string
    {
        return EshopUserModel::class;
    }
}
