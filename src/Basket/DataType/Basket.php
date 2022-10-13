<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\DataType;

use OxidEsales\Eshop\Application\Model\UserBasket as BasketModel;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use OxidEsales\GraphQL\Storefront\Basket\Event\BasketAuthorization;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Basket extends AbstractBasket implements ShopModelAwareInterface
{
    /** @var BasketModel */
    private $basket;

    public function __construct(
        BasketModel $basket
    ) {
        $this->basket = $basket;
        parent::__construct($basket);
    }

    /**
     * @Field()
     */
    public function public(): bool
    {
        return (bool)$this->basket->getRawFieldData('oxpublic');
    }

    public function getDeliveryAddressId(): ID
    {
        return new ID(
            (string)$this->basket->getRawFieldData('oegql_deladdressid')
        );
    }

    public function getDeliveryMethodId(): ID
    {
        return new ID(
            (string)$this->basket->getRawFieldData('oegql_deliverymethodid')
        );
    }

    public function getPaymentId(): ID
    {
        return new ID(
            (string)$this->basket->getRawFieldData('oegql_paymentid')
        );
    }

    public function belongsToUser(string $userId): bool
    {
        $eventDispatcher = ContainerFactory::getInstance()
            ->getContainer()
            ->get(EventDispatcherInterface::class);
        $event = new BasketAuthorization($this, new Id($userId));
        $eventDispatcher->dispatch(
            $event,
            BasketAuthorization::class,
        );

        return $event->getAuthorized();

//        return $this->getUserId()->val() === $userId;
    }

    /**
     * @return class-string
     */
    public static function getModelClass(): string
    {
        return BasketModel::class;
    }
}
