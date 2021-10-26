<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Service;

use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket as BasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketExists;
use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\BasketFactory;
use OxidEsales\GraphQL\Storefront\Basket\Infrastructure\Repository as BasketRepository;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class BasketInput
{
    /** @var Authentication */
    private $authentication;

    /** @var BasketRepository */
    private $basketRepository;

    /** @var BasketFactory */
    private $basketFactory;

    public function __construct(
        Authentication $authentication,
        BasketRepository $basketRepository,
        BasketFactory $basketFactory
    ) {
        $this->authentication   = $authentication;
        $this->basketRepository = $basketRepository;
        $this->basketFactory    = $basketFactory;
    }

    /**
     * @Factory
     */
    public function fromUserInput(string $title, bool $public = false): BasketDataType
    {
        if ($this->doesBasketExist($title)) {
            throw BasketExists::byTitle($title);
        }

        return $this->basketFactory->createBasket((string) $this->authentication->getUser()->id(), $title, $public);
    }

    private function doesBasketExist(string $title): bool
    {
        return $this->basketRepository->basketExistsByTitleAndUserId(
            $title,
            $this->authentication->getUser()->id()
        );
    }
}
