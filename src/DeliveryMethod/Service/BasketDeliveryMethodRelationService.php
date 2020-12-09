<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\DeliveryMethod\Service;

use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Basket as BasketInfrastructure;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\DataType\BasketDeliveryMethod;
use OxidEsales\GraphQL\Storefront\Shared\DataType\Price;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=BasketDeliveryMethod::class)
 */
final class BasketDeliveryMethodRelationService
{
    /** @var BasketInfrastructure */
    private $basketInfrastructure;

    public function __construct(BasketInfrastructure $basketInfrastructure)
    {
        $this->basketInfrastructure = $basketInfrastructure;
    }

    /**
     * @Field()
     */
    public function cost(BasketDeliveryMethod $basketDeliveryMethod): Price
    {
        return $this->basketInfrastructure->getDeliveryPrice($basketDeliveryMethod);
    }
}
