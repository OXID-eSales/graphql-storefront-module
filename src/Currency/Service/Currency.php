<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Currency\Service;

use OxidEsales\GraphQL\Catalogue\Currency\DataType\Currency as CurrencyDataType;
use OxidEsales\GraphQL\Catalogue\Currency\Exception\CurrencyNotFound;
use OxidEsales\GraphQL\Catalogue\Currency\Infrastructure\Repository;

final class Currency
{
    /** @var Repository */
    private $currencyRepository;

    public function __construct(
        Repository $currencyRepository
    ) {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * @throws CurrencyNotFound
     */
    public function getByName(?string $name = null): CurrencyDataType
    {
        return $name ? $this->currencyRepository->getByName($name) : $this->currencyRepository->getActiveCurrency();
    }

    /**
     * @return CurrencyDataType[]
     */
    public function getAll(): array
    {
        return $this->currencyRepository->getAll();
    }
}
