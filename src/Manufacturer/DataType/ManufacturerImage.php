<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Manufacturer\DataType;

use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use OxidEsales\Eshop\Application\Model\Manufacturer;

/**
 * @Type()
 */
final class ManufacturerImage
{
    /** @var Manufacturer */
    private $manufacturer;

    public function __construct(Manufacturer $manufacturer)
    {
        $this->manufacturer = $manufacturer;
    }

    /**
     * @Field()
     */
    public function getAlt(): string
    {
        return $this->manufacturer->getIconAltUrl();
    }

    /**
     * @Field()
     */
    public function getPicture(): string
    {
        return $this->manufacturer->getPictureUrl();
    }

    /**
     * @Field()
     */
    public function getThumbnail(): string
    {
        return $this->manufacturer->getThumbnailUrl();
    }

    /**
     * @Field()
     */
    public function getPromotion(): string
    {
        return $this->manufacturer->getPromotionIconUrl();
    }
}
