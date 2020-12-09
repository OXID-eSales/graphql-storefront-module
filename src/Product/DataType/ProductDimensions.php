<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Product\DataType;

use OxidEsales\Eshop\Application\Model\Article as EshopProductModel;
use OxidEsales\Eshop\Application\Model\OrderArticle as EshopOrderArticleModel;
use OxidEsales\Eshop\Core\Model\BaseModel;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class ProductDimensions
{
    /** @var EshopOrderArticleModel|EshopProductModel */
    private $product;

    /**
     * ProductDimensions constructor.
     *
     * @param EshopOrderArticleModel|EshopProductModel $product
     */
    public function __construct(
        BaseModel $product
    ) {
        $this->product = $product;
    }

    /**
     * @Field
     */
    public function getLength(): float
    {
        return (float) $this->product->getFieldData('oxlength');
    }

    /**
     * @Field
     */
    public function getWidth(): float
    {
        return (float) $this->product->getFieldData('oxwidth');
    }

    /**
     * @Field
     */
    public function getHeight(): float
    {
        return (float) $this->product->getFieldData('oxheight');
    }

    /**
     * @Field
     */
    public function getWeight(): float
    {
        return (float) $this->product->getFieldData('oxweight');
    }
}
