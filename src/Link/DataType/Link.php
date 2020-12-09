<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Link\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\Links as LinkModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Catalogue\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Link implements DataType
{
    /** @var LinkModel */
    private $link;

    public function __construct(
        LinkModel $link
    ) {
        $this->link = $link;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID($this->link->getId());
    }

    /**
     * @Field()
     */
    public function isActive(): bool
    {
        return (bool) $this->link->getFieldData('oxactive');
    }

    /**
     * @Field()
     */
    public function getTimestamp(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString((string) $this->link->getFieldData('oxtimestamp'));
    }

    /**
     * @Field()
     */
    public function getDescription(): string
    {
        return $this->link->getFieldData('oxurldesc');
    }

    /**
     * @Field()
     */
    public function getUrl(): string
    {
        return $this->link->getFieldData('oxurl');
    }

    /**
     * @Field()
     */
    public function getCreationDate(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString((string) $this->link->getFieldData('oxinsert'));
    }

    /**
     * @return class-string
     */
    public static function getModelClass(): string
    {
        return LinkModel::class;
    }
}
