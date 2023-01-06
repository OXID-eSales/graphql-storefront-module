<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Category\Infrastructure;

use OxidEsales\Eshop\Application\Model\Attribute as EshopAttributeModel;
use OxidEsales\Eshop\Application\Model\AttributeList as EshopAttributeListModel;
use OxidEsales\GraphQL\Storefront\Category\DataType\Category as CategoryDataType;
use OxidEsales\GraphQL\Storefront\Category\DataType\CategoryAttribute as CategoryAttributeDataType;

use function count;
use function is_iterable;

final class Category
{
    /**
     * @return CategoryAttributeDataType[]
     */
    public function getAttributes(CategoryDataType $category): array
    {
        /** @var EshopAttributeListModel $productAttributes */
        $categoryAttributes = $category->getEshopModel()->getAttributes();
    
        if (!is_iterable($categoryAttributes) || count($categoryAttributes) === 0) {
            return [];
        }
    
        $attributes = [];
    
        /** @var EshopAttributeModel $attribute */
        foreach ($categoryAttributes as $key => $attribute) {
            $attributes[$key] = new CategoryAttributeDataType($attribute);
        }
    
        return $attributes;
    }
}
