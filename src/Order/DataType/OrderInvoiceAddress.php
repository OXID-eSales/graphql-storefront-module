<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\DataType;

use OxidEsales\Eshop\Application\Model\Order as EshopOrderModel;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use OxidEsales\GraphQL\Storefront\Address\DataType\AbstractAddress;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class OrderInvoiceAddress extends AbstractAddress implements ShopModelAwareInterface
{
    public function __construct(EshopOrderModel $order)
    {
        parent::__construct($order, 'oxbill');
    }

    /**
     * @return EshopOrderModel
     */
    public function getEshopModel(): BaseModel
    {
        return $this->model;
    }

    /**
     * @Field()
     */
    public function email(): string
    {
        return $this->getFieldValue('email');
    }

    /**
     * @Field()
     */
    public function vatID(): string
    {
        return $this->getFieldValue('ustid');
    }

    public static function getModelClass(): string
    {
        return EshopOrderModel::class;
    }
}
