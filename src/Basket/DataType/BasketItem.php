<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\UserBasketItem as EshopBasketItemModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class BasketItem implements DataType
{
    /** @var EshopBasketItemModel */
    private $basketItem;

    public function __construct(EshopBasketItemModel $basketItem)
    {
        $this->basketItem = $basketItem;
    }

    public function getEshopModel(): EshopBasketItemModel
    {
        return $this->basketItem;
    }

    /**
     * @Field()
     */
    public function id(): ID
    {
        return new ID(
            $this->basketItem->getId()
        );
    }

    /**
     * @Field()
     */
    public function amount(): int
    {
        return (int) $this->basketItem->getRawFieldData('oxamount');
    }

    /**
     * @Field()
     */
    public function lastUpdateDate(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->basketItem->getRawFieldData('oxtimestamp')
        );
    }

    public function basketId(): string
    {
        return (string) $this->basketItem->getRawFieldData('oxbasketid');
    }

    public static function getModelClass(): string
    {
        return EshopBasketItemModel::class;
    }
}
