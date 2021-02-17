<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Translation\Controller;

use OxidEsales\GraphQL\Storefront\Translation\DataType\Translation as TranslationDataType;
use OxidEsales\GraphQL\Storefront\Translation\Service\Translation as TranslationService;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class Translation
{
    /** @var TranslationService */
    private $translationService;

    public function __construct(
        TranslationService $translationService
    ) {
        $this->translationService = $translationService;
    }

    /**
     * @Query
     */
    public function translation(string $key): TranslationDataType
    {
        return $this->translationService->getTranslation($key);
    }

    /**
     * @Query
     *
     * @return TranslationDataType[]
     */
    public function translations(): array
    {
        return $this->translationService->getTranslations();
    }
}
