<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Attribute\DataType;

use OxidEsales\Eshop\Application\Model\Attribute as EshopAttributeModel;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class Attribute implements DataType
{
    /** @var EshopAttributeModel */
    private $attribute;

    public function __construct(EshopAttributeModel $attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * @Field()
     */
    public function getTitle(): string
    {
        return (string) $this->attribute->getRawFieldData('oxtitle');
    }

    public static function getModelClass(): string
    {
        return EshopAttributeModel::class;
    }
}
