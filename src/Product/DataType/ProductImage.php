<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Product\DataType;

use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class ProductImage
{
    /** @var string */
    private $image;

    /** @var string */
    private $icon;

    /** @var string */
    private $zoom;

    public function __construct(string $image, string $icon, string $zoom)
    {
        $this->image = $image;
        $this->icon  = $icon;
        $this->zoom  = $zoom;
    }

    /**
     * @Field()
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @Field()
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @Field()
     */
    public function getZoom(): string
    {
        return $this->zoom;
    }
}
