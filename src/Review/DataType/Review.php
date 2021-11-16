<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Review\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\Review as EshopReviewModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Review implements DataType
{
    /** @var EshopReviewModel */
    private $review;

    public function __construct(EshopReviewModel $review)
    {
        $this->review = $review;
    }

    public function getEshopModel(): EshopReviewModel
    {
        return $this->review;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID($this->review->getId());
    }

    /**
     * @Field()
     */
    public function getText(): string
    {
        return (string) $this->review->getRawFieldData('oxtext');
    }

    /**
     * @Field()
     */
    public function getRating(): int
    {
        return (int) $this->review->getRawFieldData('oxrating');
    }

    /**
     * @Field()
     */
    public function getCreateAt(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->review->getRawFieldData('oxcreate')
        );
    }

    public function getReviewerId(): string
    {
        return (string) $this->review->getRawFieldData('oxuserid');
    }

    public function isArticleType(): bool
    {
        return (bool) ((string) $this->review->getRawFieldData('oxtype') === 'oxarticle');
    }

    public function getObjectId(): string
    {
        return (string) $this->review->getRawFieldData('oxobjectid');
    }

    public static function getModelClass(): string
    {
        return EshopReviewModel::class;
    }
}
