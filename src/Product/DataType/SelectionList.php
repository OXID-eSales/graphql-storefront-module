<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\DataType;

use OxidEsales\Eshop\Application\Model\SelectList as EshopSelectionListModel;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class SelectionList
{
    /** @var EshopSelectionListModel */
    private $selectionList;

    /**
     * SelectionList constructor.
     */
    public function __construct(EshopSelectionListModel $selectionList)
    {
        $this->selectionList = $selectionList;
    }

    /**
     * @Field()
     */
    public function getTitle(): string
    {
        return (string) $this->selectionList->getFieldData('oxtitle');
    }

    /**
     * @Field()
     *
     * @return Selection[]
     */
    public function getFields(): array
    {
        $fields = [];

        foreach ($this->selectionList->getSelections() as $field) {
            $fields[] = new Selection($field);
        }

        return $fields;
    }
}
