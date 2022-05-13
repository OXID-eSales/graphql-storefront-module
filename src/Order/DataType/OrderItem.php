<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\DataType;

use DateTimeImmutable;
use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\OrderArticle as EshopOrderArticleModel;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use OxidEsales\GraphQL\Storefront\Product\DataType\ProductDimensions;
use OxidEsales\GraphQL\Storefront\Shared\DataType\Price;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class OrderItem implements ShopModelAwareInterface
{
    /** @var EshopOrderArticleModel */
    private $orderArticle;

    public function __construct(EshopOrderArticleModel $orderArticle)
    {
        $this->orderArticle = $orderArticle;
    }

    public function getEshopModel(): EshopOrderArticleModel
    {
        return $this->orderArticle;
    }

    /**
     * @Field
     */
    public function id(): ID
    {
        return new ID($this->orderArticle->getId());
    }

    /**
     * @Field
     */
    public function amount(): float
    {
        return (float)$this->orderArticle->getRawFieldData('OXAMOUNT');
    }

    /**
     * @Field
     */
    public function sku(): string
    {
        return (string)$this->orderArticle->getRawFieldData('OXARTNUM');
    }

    /**
     * @Field
     */
    public function title(): string
    {
        return (string)$this->orderArticle->getRawFieldData('OXTITLE');
    }

    /**
     * @Field
     */
    public function shortDescription(): string
    {
        return (string)$this->orderArticle->getRawFieldData('OXSHORTDESC');
    }

    /**
     * @Field()
     */
    public function getPrice(): Price
    {
        /** @var \OxidEsales\Eshop\Core\Price $totalPrice */
        $totalPrice = $this->orderArticle->getPrice();

        return new Price($totalPrice);
    }

    /**
     * @Field()
     */
    public function getItemPrice(): Price
    {
        /** @var \OxidEsales\Eshop\Core\Price $itemPrice */
        $itemPrice = $this->orderArticle->getBasePrice();

        return new Price($itemPrice);
    }

    /**
     * @Field
     */
    public function getDimensions(): ProductDimensions
    {
        return new ProductDimensions($this->orderArticle);
    }

    /**
     * @Field()
     */
    public function getInsert(): DateTimeInterface
    {
        return new DateTimeImmutable(
            (string)$this->orderArticle->getRawFieldData('OXINSERT')
        );
    }

    /**
     * @Field()
     */
    public function getTimestamp(): DateTimeInterface
    {
        return new DateTimeImmutable(
            (string)$this->orderArticle->getRawFieldData('OXTIMESTAMP')
        );
    }

    /**
     * @Field()
     */
    public function isCancelled(): bool
    {
        return (bool)$this->orderArticle->getRawFieldData('OXSTORNO');
    }

    /**
     * @Field()
     */
    public function isBundle(): bool
    {
        return (bool)$this->orderArticle->getRawFieldData('OXISBUNDLE');
    }

    public function productId(): string
    {
        return (string)$this->orderArticle->getRawFieldData('OXARTID');
    }

    public static function getModelClass(): string
    {
        return EshopOrderArticleModel::class;
    }
}
