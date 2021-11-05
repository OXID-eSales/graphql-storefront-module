<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Promotion\DataType;

use OxidEsales\Eshop\Application\Model\Actions as EshopActionsModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Promotion implements ShopModelAwareInterface
{
    public const PROMOTION_ACTION_TYPE = '2';

    /** @var EshopActionsModel */
    private $actionsModel;

    public function __construct(EshopActionsModel $actionsModel)
    {
        $this->actionsModel = $actionsModel;

        if ($actionsModel->getFieldData('oxtype') !== self::PROMOTION_ACTION_TYPE) {
            throw NotFound::notFound();
        }
    }

    public function getEshopModel(): EshopActionsModel
    {
        return $this->actionsModel;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID($this->actionsModel->getId());
    }

    /**
     * @Field()
     */
    public function isActive(): bool
    {
        return (bool) $this->actionsModel->getFieldData('oxactive') && $this->isActiveNow();
    }

    /**
     * @Field()
     */
    public function getTitle(): string
    {
        return $this->actionsModel->getFieldData('oxtitle');
    }

    /**
     * @Field()
     */
    public function getText(): string
    {
        return $this->actionsModel->getFieldData('oxlongdesc');
    }

    private function isActiveNow(): bool
    {
        $activeNow = false;

        $from = DateTimeImmutableFactory::fromString(
            (string) $this->actionsModel->getFieldData('oxactivefrom')
        );
        $to = DateTimeImmutableFactory::fromString(
            (string) $this->actionsModel->getFieldData('oxactiveto')
        );
        $now = $now ?? DateTimeImmutableFactory::fromString('now');

        if ($from <= $now && $to >= $now) {
            $activeNow = true;
        }

        return $activeNow;
    }

    /**
     * @return class-string
     */
    public static function getModelClass(): string
    {
        return EshopActionsModel::class;
    }
}
