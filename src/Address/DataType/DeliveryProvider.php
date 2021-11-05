<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\DataType;

use OxidEsales\Eshop\Application\Model\DeliverySet as EshopDeliveryProviderModel;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class DeliveryProvider implements ShopModelAwareInterface
{
    /** @var EshopDeliveryProviderModel */
    private $order;

    public function __construct(EshopDeliveryProviderModel $order)
    {
        $this->order = $order;
    }

    public function getEshopModel(): EshopDeliveryProviderModel
    {
        return $this->order;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID((string) $this->order->getId());
    }

    /**
     * @Field()
     */
    public function getActive(): bool
    {
        return (bool) ($this->order->getFieldData('oxactive'));
    }

    /**
     * @Field()
     */
    public function getTitle(): string
    {
        return (string) ($this->order->getFieldData('oxtitle'));
    }

    public static function getModelClass(): string
    {
        return EshopDeliveryProviderModel::class;
    }
}
