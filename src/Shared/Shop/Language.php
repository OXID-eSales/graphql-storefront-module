<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Shop;

/**
 * Core Language extended
 *
 * @mixin Language
 * @eshopExtension
 */
class Language extends Language_parent
{
    /**
     * @return array<string, string>
     */
    public function getTranslationKeys(int $languageId): array
    {
        return $this->getLanguageFileData(false, $languageId);
    }
}
