<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Translation\DataType;

use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class Translation
{
    /** @var string */
    private $key;

    /** @var string */
    private $value;

    public function __construct(
        string $key,
        string $value
    ) {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @Field()
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @Field()
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
