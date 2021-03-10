<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Infrastructure;

use OxidEsales\Eshop\Core\Registry;

final class LanguageInfrastructure
{
    public function getLanguageCode(int $languageId): string
    {
        return Registry::getLang()->getLanguageAbbr($languageId);
    }

    public function getLanguageName(int $languageId): string
    {
        $languageNames = Registry::getLang()->getLanguageNames();

        return $languageNames[$languageId];
    }
}
