<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Action\DataType;

use OxidEsales\Eshop\Application\Model\Actions as EshopActionsModel;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\ArticleList;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Action implements ShopModelAwareInterface
{
    public const ACTION_TYPE = [0, 1];

    /** @var EshopActionsModel */
    private $action;

    public function __construct(EshopActionsModel $action)
    {
        $this->action = $action;

        if (!in_array($action->getRawFieldData('oxtype'), self::ACTION_TYPE)) {
            throw new NotFound();
        }
    }

    public function getEshopModel(): EshopActionsModel
    {
        return $this->action;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID($this->action->getId());
    }

    /**
     * @Field()
     */
    public function isActive(): bool
    {
        return (bool)$this->action->getRawFieldData('oxactive');
    }

    /**
     * @Field()
     */
    public function getTitle(): string
    {
        return (string)$this->action->getRawFieldData('oxtitle');
    }

    /**
     * @Field
     *
     * @return Product[]
     */
    public function getProducts(): array
    {
        /** @var ArticleList $oArtList */
        $oArtList = oxNew(ArticleList::class);
        $oArtList->loadActionArticles($this->action->getId());

        $products = [];

        /** @var Article $product */
        foreach ($oArtList as $product) {
            $products[] = new Product($product);
        }

        return $products;
    }

    public static function getModelClass(): string
    {
        return EshopActionsModel::class;
    }
}
