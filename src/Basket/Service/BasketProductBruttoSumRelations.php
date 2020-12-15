<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Service;

use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketProductBruttoSum;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketProductVats;
use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\BasketProduct as BasketProductInfrastructure;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=BasketProductBruttoSum::class)
 */
final class BasketProductBruttoSumRelations
{
    /** @var BasketProductInfrastructure */
    private $basketProductInfrastructure;

    public function __construct(
        BasketProductInfrastructure $basketProductInfrastructure
    ) {
        $this->basketProductInfrastructure = $basketProductInfrastructure;
    }

    /**
     * @Field()
     *
     * @return BasketProductVats[]
     */
    public function getVats(BasketProductBruttoSum $basketProductGross): array
    {
        return $this->basketProductInfrastructure->getVats($basketProductGross);
    }
}
