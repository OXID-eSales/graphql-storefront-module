<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\UserBasket as BasketModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Types\ID;

abstract class AbstractBasket
{
    /** @var BasketModel */
    private $basket;

    public function __construct(
        BasketModel $basket
    ) {
        $this->basket = $basket;
    }

    public function getEshopModel(): BasketModel
    {
        return $this->basket;
    }

    /**
     * @Field()
     */
    public function id(): ID
    {
        return new ID(
            $this->basket->getId()
        );
    }

    /**
     * @Field()
     * Beware of the following values with special meaning
     * - wishList
     * - noticeList
     * - savedBasket
     */
    public function title(): string
    {
        return (string)$this->basket->getRawFieldData('oxtitle');
    }

    /**
     * @Field()
     */
    public function creationDate(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString((string)$this->basket->getRawFieldData('oxtimestamp'));
    }

    /**
     * @Field()
     */
    public function lastUpdateDate(): ?DateTimeInterface
    {
        $timeStamp = (int)$this->basket->getRawFieldData('oxupdate');

        if ($timeStamp > 0) {
            return DateTimeImmutableFactory::fromTimeStamp($timeStamp);
        }

        return null;
    }

    public function getUserId(): ID
    {
        return new ID(
            (string)$this->basket->getRawFieldData('oxuserid')
        );
    }
}
