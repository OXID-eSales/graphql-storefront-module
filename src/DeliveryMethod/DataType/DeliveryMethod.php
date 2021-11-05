<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\DeliveryMethod\DataType;

use OxidEsales\Eshop\Application\Model\DeliverySet as EshopDeliverySetModel;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use OxidEsales\GraphQL\Storefront\Payment\DataType\BasketPayment as BasketPaymentDataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 * @extendable-dataType
 */
class DeliveryMethod implements ShopModelAwareInterface
{
    /** @var EshopDeliverySetModel */
    private $deliverySetModel;

    /** @var BasketPaymentDataType[] */
    private $basketPaymentTypes;

    public function __construct(
        EshopDeliverySetModel $deliverySetModel,
        array $basketPaymentTypes = []
    ) {
        $this->deliverySetModel       = $deliverySetModel;
        $this->basketPaymentTypes     = $basketPaymentTypes;
    }

    public function getEshopModel(): EshopDeliverySetModel
    {
        return $this->deliverySetModel;
    }

    /**
     * @Field()
     */
    public function id(): ID
    {
        return new ID(
            $this->deliverySetModel->getId()
        );
    }

    /**
     * @Field()
     */
    public function title(): string
    {
        return (string) $this->deliverySetModel->getFieldData('oxtitle');
    }

    /**
     * @Field()
     *
     * @return BasketPaymentDataType[]
     */
    public function getPaymentTypes(): array
    {
        return $this->basketPaymentTypes;
    }

    /**
     * @Field()
     */
    public function getPosition(): int
    {
        return (int) $this->deliverySetModel->getFieldData('oxpos');
    }

    public static function getModelClass(): string
    {
        return EshopDeliverySetModel::class;
    }
}
