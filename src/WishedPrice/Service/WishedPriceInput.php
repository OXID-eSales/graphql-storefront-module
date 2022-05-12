<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Service;

use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product as ProductDataType;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\WishedPrice\DataType\WishedPrice;
use OxidEsales\GraphQL\Storefront\WishedPrice\Exception\WishedPriceOutOfBounds;
use OxidEsales\GraphQL\Storefront\WishedPrice\Infrastructure\WishedPriceFactory;
use TheCodingMachine\GraphQLite\Annotations\Factory;
use TheCodingMachine\GraphQLite\Types\ID;

final class WishedPriceInput
{
    /** @var Authentication */
    private $authentication;

    /** @var Repository */
    private $repository;

    /** @var WishedPriceFactory */
    private $wishedPriceFactory;

    public function __construct(
        Authentication $authentication,
        Repository $repository,
        WishedPriceFactory $wishedPriceFactory
    ) {
        $this->authentication = $authentication;
        $this->repository = $repository;
        $this->wishedPriceFactory = $wishedPriceFactory;
    }

    /**
     * @Factory
     */
    public function fromUserInput(ID $productId, string $currencyName, float $price): WishedPrice
    {
        $this->assertProductWishedPriceIsPossible($productId);
        $this->assertPriceValue($price);

        return $this->wishedPriceFactory->createWishedPrice(
            (string)$this->authentication->getUser()->id(),
            $this->authentication->getUser()->email(),
            $productId,
            $currencyName,
            $price
        );
    }

    /**
     * @return true
     * @throws ProductNotFound
     *
     */
    private function assertProductWishedPriceIsPossible(ID $productId): bool
    {
        $id = (string)$productId->val();

        try {
            /** @var ProductDataType $product */
            $product = $this->repository->getById($id, ProductDataType::class);
        } catch (NotFound $e) {
            throw ProductNotFound::byId($id);
        }

        // Throw 404 if product has wished prices disabled
        if (!$product->getEshopModel()->isPriceAlarm()) {
            throw ProductNotFound::byId($id);
        }

        return true;
    }

    /**
     * @return true
     * @throws WishedPriceOutOfBounds
     *
     */
    private function assertPriceValue(float $price): bool
    {
        if ($price <= 0) {
            throw WishedPriceOutOfBounds::byValue($price);
        }

        return true;
    }
}
