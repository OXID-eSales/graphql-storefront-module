<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\DataType;

use OxidEsales\Eshop\Application\Model\VariantSelectList as EshopVariantSelectionListModel;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class VariantSelectionList
{
    /** @var EshopVariantSelectionListModel */
    private EshopVariantSelectionListModel $variantSelectList;

    /**
     * constructor
     */
    public function __construct(EshopVariantSelectionListModel $selectionList)
    {
        $this->variantSelectList = $selectionList;
    }

    /**
     * @Field()
     */
    public function getLabel(): string
    {
        return (string) $this->variantSelectList->getLabel();
    }

    /**
     *  @Field()
     */
    public function getActiveSelection(): ?Selection
    {
        if ($activeSelection = $this->variantSelectList->getActiveSelection()) {
            return new Selection($activeSelection);
        }

        return null;
    }

    /**
     * @Field()
     *
     * @return Selection[]
     */
    public function getFields(): array
    {
        $fields = [];

        foreach ($this->variantSelectList->getSelections() as $field) {
            $fields[] = new Selection($field);
        }

        return $fields;
    }
}
