<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\DataType;

use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Language
{
    /** @var int */
    private $languageId;

    public function __construct(int $languageId)
    {
        $this->languageId = $languageId;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID($this->getLanguageId());
    }

    public function getLanguageId(): int
    {
        return $this->languageId;
    }
}
