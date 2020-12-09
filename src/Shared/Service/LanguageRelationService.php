<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Shared\Service;

use OxidEsales\GraphQL\Catalogue\Shared\DataType\Language;
use OxidEsales\GraphQL\Catalogue\Shared\Infrastructure\LanguageInfrastructure;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=Language::class)
 */
final class LanguageRelationService
{
    /** @var LanguageInfrastructure */
    private $languageInfrastructure;

    public function __construct(
        LanguageInfrastructure $languageInfrastructure
    ) {
        $this->languageInfrastructure = $languageInfrastructure;
    }

    /**
     * @Field()
     */
    public function getCode(Language $language): string
    {
        return $this->languageInfrastructure->getLanguageCode($language->getLanguageId());
    }

    /**
     * @Field()
     */
    public function getLanguage(Language $language): string
    {
        return $this->languageInfrastructure->getLanguageName($language->getLanguageId());
    }
}
