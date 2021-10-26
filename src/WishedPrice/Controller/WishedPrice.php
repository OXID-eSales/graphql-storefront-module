<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Controller;

use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Storefront\WishedPrice\DataType\WishedPrice as WishedPriceDataType;
use OxidEsales\GraphQL\Storefront\WishedPrice\DataType\WishedPriceFilterList;
use OxidEsales\GraphQL\Storefront\WishedPrice\Service\WishedPrice as WishedPriceService;
use TheCodingMachine\GraphQLite\Annotations\HideIfUnauthorized;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Types\ID;

final class WishedPrice
{
    /** @var WishedPriceService */
    private $wishedPriceService;

    public function __construct(
        WishedPriceService $wishedPriceService
    ) {
        $this->wishedPriceService = $wishedPriceService;
    }

    /**
     * @Query()
     */
    public function wishedPrice(ID $wishedPriceId): WishedPriceDataType
    {
        return $this->wishedPriceService->wishedPrice($wishedPriceId);
    }

    /**
     * @Query()
     *
     * @throws InvalidToken
     *
     * @return WishedPriceDataType[]
     */
    public function wishedPrices(): array
    {
        return $this->wishedPriceService->wishedPrices(
            new WishedPriceFilterList()
        );
    }

    /**
     * @Mutation()
     * @Logged()
     * @HideIfUnauthorized()
     */
    public function wishedPriceSet(WishedPriceDataType $wishedPrice): WishedPriceDataType
    {
        $this->wishedPriceService->save($wishedPrice);

        return $wishedPrice;
    }

    /**
     * @Mutation()
     * @Logged()
     * @HideIfUnauthorized()
     */
    public function wishedPriceDelete(ID $wishedPriceId): bool
    {
        return $this->wishedPriceService->delete($wishedPriceId);
    }
}
