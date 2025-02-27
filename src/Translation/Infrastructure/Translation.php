<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Translation\Infrastructure;

final class Translation
{
    /**
     * @return array<string, string>
     */
    public function getTranslations(int $languageId): array
    {
        /** @var \OxidEsales\GraphQL\Storefront\Shared\Shop\Language $language */
        $language = \OxidEsales\Eshop\Core\Registry::getLang();

        return $language->getTranslationKeys($languageId);
    }
}
