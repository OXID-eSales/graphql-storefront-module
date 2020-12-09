<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Currency\Controller;

use OxidEsales\GraphQL\Catalogue\Currency\DataType\Currency as CurrencyDataType;
use OxidEsales\GraphQL\Catalogue\Currency\Service\Currency as CurrencyService;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class Currency
{
    /** @var CurrencyService */
    private $currencyService;

    public function __construct(
        CurrencyService $currencyService
    ) {
        $this->currencyService = $currencyService;
    }

    /**
     * If `name` is ommited, gives you the currently active currency
     *
     * @Query()
     */
    public function currency(?string $name = null): CurrencyDataType
    {
        return $this->currencyService->getByName($name);
    }

    /**
     * @Query()
     *
     * @return CurrencyDataType[]
     */
    public function currencies(): array
    {
        return $this->currencyService->getAll();
    }
}
