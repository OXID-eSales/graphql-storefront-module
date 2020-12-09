<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\DataType;

use OxidEsales\Eshop\Application\Model\Selection as EshopSelectionModel;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class Selection
{
    /** @var EshopSelectionModel */
    private $selection;

    /**
     * Selection constructor.
     */
    public function __construct(EshopSelectionModel $selection)
    {
        $this->selection = $selection;
    }

    /**
     * @Field()
     */
    public function getValue(): string
    {
        return (string) $this->selection->getName();
    }
}
