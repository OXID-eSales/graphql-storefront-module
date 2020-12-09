<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Shared\Infrastructure;

use OxidEsales\Eshop\Core\Language as LanguageService;

final class LanguageInfrastructure
{
    /** @var LanguageService */
    private $languageService;

    public function __construct(
        LanguageService $languageService
    ) {
        $this->languageService = $languageService;
    }

    public function getLanguageCode(int $languageId): string
    {
        return $this->languageService->getLanguageAbbr($languageId);
    }

    public function getLanguageName(int $languageId): string
    {
        $languageNames = $this->languageService->getLanguageNames();

        return $languageNames[$languageId];
    }
}
