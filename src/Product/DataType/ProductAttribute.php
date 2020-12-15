<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\DataType;

use OxidEsales\Eshop\Application\Model\Attribute as EshopAttributeModel;
use OxidEsales\GraphQL\Storefront\Attribute\DataType\Attribute;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class ProductAttribute
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
    public function getAttribute(): Attribute
    {
        return new Attribute($this->attribute);
    }

    /**
     * @Field()
     */
    public function getValue(): string
    {
        return (string) $this->attribute->getFieldData('oxvalue');
    }
}
