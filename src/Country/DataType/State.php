<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Country\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\State as EshopStateModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class State implements DataType
{
    /** @var EshopStateModel */
    private $state;

    public function __construct(EshopStateModel $state)
    {
        $this->state = $state;
    }

    public function getEshopModel(): EshopStateModel
    {
        return $this->state;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID($this->state->getId());
    }

    /**
     * @Field()
     */
    public function getTitle(): string
    {
        return (string) $this->state->getFieldData('oxtitle');
    }

    /**
     * @Field()
     */
    public function getIsoAlpha2(): string
    {
        return (string) $this->state->getFieldData('oxisoalpha2');
    }

    /**
     * @Field()
     */
    public function getCreationDate(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString((string) $this->state->getFieldData('oxtimestamp'));
    }

    public static function getModelClass(): string
    {
        return EshopStateModel::class;
    }
}
