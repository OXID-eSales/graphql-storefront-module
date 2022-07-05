<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Translation\Service;

use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyInfrastructure;
use OxidEsales\GraphQL\Storefront\Translation\DataType\Translation as TranslationDataType;
use OxidEsales\GraphQL\Storefront\Translation\Exception\TranslationNotFound;
use OxidEsales\GraphQL\Storefront\Translation\Infrastructure\Translation as TranslationInfrastructure;

final class Translation
{
    /** @var TranslationInfrastructure */
    private $translationInfrastructure;

    /** @var LegacyInfrastructure */
    private $legacyInfrastructure;

    public function __construct(
        TranslationInfrastructure $translationInfrastructure,
        LegacyInfrastructure $legacyInfrastructure
    ) {
        $this->translationInfrastructure = $translationInfrastructure;
        $this->legacyInfrastructure = $legacyInfrastructure;
    }

    /**
     * @return TranslationDataType[]
     */
    public function getTranslations(): array
    {
        $languageId = $this->legacyInfrastructure->getLanguageId();
        $translationsList = $this->translationInfrastructure->getTranslations($languageId);

        $result = [];

        foreach ($translationsList as $key => $value) {
            if (!is_string($value)) {
                continue;
            }

            $result[] = new TranslationDataType($key, $value);
        }

        return $result;
    }

    public function getTranslation(string $key): TranslationDataType
    {
        $languageId = $this->legacyInfrastructure->getLanguageId();
        $translations = $this->translationInfrastructure->getTranslations($languageId);

        if (!isset($translations[$key])) {
            throw new TranslationNotFound($key);
        }

        return new TranslationDataType($key, $translations[$key]);
    }
}
