<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\DataType;

use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
class VariantSelections
{
    private array $variantSelections;

    /**
     * @param array $variantSelections
     */
    public function __construct(array $variantSelections)
    {
        $this->variantSelections = $variantSelections;
    }

    /**
     * @Field()
     *
     * @return VariantSelectionList[]
     */
    public function getSelections(): array
    {
        $variantSelectionList = [];

        if (!isset($this->variantSelections['selections']) || !count($this->variantSelections['selections']) ) {
            return $variantSelectionList;
        }

        foreach ($this->variantSelections['selections'] as $variantSelection) {
            $variantSelectionList[] = new VariantSelectionList($variantSelection);
        }

        return $variantSelectionList;
    }

    /**
     * @Field()
     *
     * @return ?Product
     */
    public function getActiveVariant(): ?Product
    {
        if (!isset($this->variantSelections['oActiveVariant'])) {
            return null;
        }

        return new Product($this->variantSelections['oActiveVariant']);
    }
}
