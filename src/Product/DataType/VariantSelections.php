<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\DataType;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\VariantSelectList;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

#[Type]
class VariantSelections
{
    /**
     * @var array{
     *     selections: null|VariantSelectList[],
     *     oActiveVariant: Article,
     *     blPerfectFit: bool
     * }
     */
    private array $variantSelections;

    /**
     * @param array{
     *     selections: null|VariantSelectList[],
     *     oActiveVariant: Article,
     *     blPerfectFit: bool
     * } $variantSelections
     */
    public function __construct(array $variantSelections)
    {
        $this->variantSelections = $variantSelections;
    }

    /**
     * @return VariantSelectionList[]
     */
    #[Field]
    public function getSelections(): array
    {
        $variantSelectionList = [];

        if (!isset($this->variantSelections['selections']) || !count($this->variantSelections['selections'])) {
            return $variantSelectionList;
        }

        foreach ($this->variantSelections['selections'] as $variantSelection) {
            $variantSelectionList[] = new VariantSelectionList($variantSelection);
        }

        return $variantSelectionList;
    }

    /**
     * @return ?Product
     */
    #[Field]
    public function getActiveVariant(): ?Product
    {
        if (!isset($this->variantSelections['oActiveVariant']) || !$this->variantSelections['blPerfectFit']) {
            return null;
        }

        return new Product($this->variantSelections['oActiveVariant']);
    }
}
