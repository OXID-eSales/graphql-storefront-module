<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Content\DataType;

use OxidEsales\Eshop\Application\Model\Content as EshopContentModel;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Content implements DataType
{
    public const TYPE_CATEGORY = 2;

    /** @var EshopContentModel */
    private $content;

    public function __construct(
        EshopContentModel $content
    ) {
        $this->content = $content;
    }

    public function getEshopModel(): EshopContentModel
    {
        return $this->content;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID($this->content->getId());
    }

    /**
     * @Field()
     */
    public function isActive(): bool
    {
        return (bool) $this->content->isActive();
    }

    /**
     * @Field()
     */
    public function getTitle(): string
    {
        return $this->content->getTitle();
    }

    /**
     * @Field()
     */
    public function getContent(): string
    {
        return $this->content->getFieldData('oxcontent');
    }

    /**
     * @Field()
     */
    public function getFolder(): string
    {
        return $this->content->getFieldData('oxfolder');
    }

    /**
     * @Field()
     */
    public function getVersion(): string
    {
        return $this->content->getFieldData('oxtermversion');
    }

    /**
     * @return class-string
     */
    public static function getModelClass(): string
    {
        return EshopContentModel::class;
    }
}
