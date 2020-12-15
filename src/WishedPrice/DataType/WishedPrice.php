<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\PriceAlarm as WishedPriceModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class WishedPrice implements DataType
{
    /** @var WishedPriceModel */
    private $wishedPrice;

    public function __construct(
        WishedPriceModel $wishedPrice
    ) {
        $this->wishedPrice = $wishedPrice;
    }

    public function getEshopModel(): WishedPriceModel
    {
        return $this->wishedPrice;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID(
            $this->wishedPrice->getId()
        );
    }

    public function getInquirerId(): ID
    {
        return new ID(
            (string) $this->wishedPrice->getFieldData('oxuserid')
        );
    }

    public function getProductId(): ID
    {
        return new ID(
            (string) $this->wishedPrice->getFieldData('oxartid')
        );
    }

    /**
     * @Field()
     */
    public function getEmail(): string
    {
        return $this->wishedPrice->getFieldData('oxemail');
    }

    /**
     * This field gives us information about the last sent notification email.
     * When it is null it states that no notification email was sent.
     *
     * @Field()
     */
    public function getNotificationDate(): ?DateTimeInterface
    {
        $notificationDate = (string) $this->wishedPrice->getFieldData('oxsended');

        return DateTimeImmutableFactory::fromString($notificationDate);
    }

    /**
     * @Field()
     */
    public function getCreationDate(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString((string) $this->wishedPrice->getFieldData('oxinsert'));
    }

    /**
     * @return class-string
     */
    public static function getModelClass(): string
    {
        return WishedPriceModel::class;
    }
}
