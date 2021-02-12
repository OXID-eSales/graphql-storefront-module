<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Translation\Service;

use OxidEsales\GraphQL\Storefront\Translation\Exception\TranslationNotFound;
use OxidEsales\GraphQL\Storefront\Translation\Infrastructure\Translation as TranslationInfrastructure;
use OxidEsales\GraphQL\Storefront\Translation\DataType\Translation as TranslationDataType;

final class Translation
{
    /** @var TranslationInfrastructure */
    private $translationInfrastructure;

    public function __construct(
        TranslationInfrastructure $translationInfrastructure
    ) {
        $this->translationInfrastructure = $translationInfrastructure;
    }

    /**
     * @return TranslationDataType[]
     */
    public function getTranslations(): array
    {
        $translationsList = $this->translationInfrastructure->getTranslations(0);

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
        $translations = $this->translationInfrastructure->getTranslations(0);

        if (!isset($translations[$key])) {
            throw TranslationNotFound::byKey($key);
        }

        return new TranslationDataType($key, $translations[$key]);
    }
}
