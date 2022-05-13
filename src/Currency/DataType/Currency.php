<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Currency\DataType;

use stdClass;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class Currency
{
    /** @var stdClass */
    private $currency;

    public function __construct(stdClass $currencyObject)
    {
        $this->currency = $currencyObject;
    }

    /**
     * @Field()
     */
    public function getId(): int
    {
        return $this->currency->id;
    }

    /**
     * @Field()
     */
    public function getName(): string
    {
        return $this->currency->name;
    }

    /**
     * @Field()
     */
    public function getRate(): float
    {
        return (float)$this->currency->rate;
    }

    /**
     * @Field()
     */
    public function getSign(): string
    {
        return $this->currency->sign;
    }

    public function getPrecision(): int
    {
        return (int)$this->currency->decimal;
    }
}
