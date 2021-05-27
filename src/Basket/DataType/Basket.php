<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\UserBasket as BasketModel;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Storefront\Basket\Event\BasketAuthorization;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Basket implements DataType
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
        return (string) $this->basket->getFieldData('oxtitle');
    }

    /**
     * @Field()
     */
    public function public(): bool
    {
        return (bool) $this->basket->getFieldData('oxpublic');
    }

    /**
     * @Field()
     */
    public function creationDate(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString((string) $this->basket->getFieldData('oxtimestamp'));
    }

    /**
     * @Field()
     */
    public function lastUpdateDate(): ?DateTimeInterface
    {
        $timeStamp = (int) $this->basket->getFieldData('oxupdate');

        if ($timeStamp > 0) {
            return DateTimeImmutableFactory::fromTimeStamp($timeStamp);
        }

        return null;
    }

    public function getUserId(): ID
    {
        return new ID(
            (string) $this->basket->getFieldData('oxuserid')
        );
    }

    public function belongsToUser(string $userId): bool
    {
        $eventDispatcher = ContainerFactory::getInstance()
            ->getContainer()
            ->get(EventDispatcherInterface::class);
        $event = new BasketAuthorization($this->id(), new Id($userId));
        $eventDispatcher->dispatch(
            $event,
            BasketAuthorization::NAME,
        );

        $isAuthorized = $event->getAuthorized();

        if (is_bool($isAuthorized)) {
            return $isAuthorized;
        }

        return (string) $this->getUserId() === $userId;
    }

    /**
     * @return class-string
     */
    public static function getModelClass(): string
    {
        return BasketModel::class;
    }
}
