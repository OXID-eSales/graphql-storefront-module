<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\OrderFile as OrderFileModel;
use OxidEsales\Eshop\Core\Registry as EshopRegistry;
use OxidEsales\Eshop\Core\SeoEncoder as EshopSeoEncoder;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class OrderFile implements DataType
{
    /** @var OrderFileModel */
    private $orderFile;

    public function __construct(
        OrderFileModel $orderFile
    ) {
        $this->orderFile = $orderFile;
    }

    /**
     * @Field()
     */
    public function id(): ID
    {
        return new ID($this->orderFile->getId());
    }

    /**
     * @Field()
     */
    public function filename(): string
    {
        return (string) $this->orderFile->getFieldData('OXFILENAME');
    }

    /**
     * @Field()
     */
    public function firstDownload(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->orderFile->getFieldData('OXFIRSTDOWNLOAD')
        );
    }

    /**
     * @Field()
     */
    public function latestDownload(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->orderFile->getFieldData('OXLASTDOWNLOAD')
        );
    }

    /**
     * @Field()
     */
    public function downloadCount(): int
    {
        return (int) $this->orderFile->getFieldData('OXDOWNLOADCOUNT');
    }

    /**
     * @Field()
     */
    public function maxDownloadCount(): int
    {
        return (int) $this->orderFile->getFieldData('OXMAXDOWNLOADCOUNT');
    }

    /**
     * @Field()
     */
    public function validUntil(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->orderFile->getFieldData('OXVALIDUNTIL')
        );
    }

    /**
     * @Field()
     */
    public function valid(): bool
    {
        return (bool) $this->orderFile->isValid();
    }

    /**
     * @Field()
     */
    public function url(): string
    {
        /** @var EshopSeoEncoder $seoEncoder */
        $seoEncoder  = EshopRegistry::getSeoEncoder();
        $shopUrl     = EshopRegistry::getConfig()->getShopHomeUrl();
        $downloadUrl = $shopUrl . 'cl=download';
        $seoUrl      = $seoEncoder->getStaticUrl($downloadUrl) ? $seoEncoder->getStaticUrl($downloadUrl) : $downloadUrl;

        //Take care of some extra parameter after seo url
        $filePath = (strpos($seoUrl, '?') !== false ? '&' : '?') . sprintf('sorderfileid=%s', $this->id());

        return htmlspecialchars_decode($seoUrl . $filePath);
    }

    public function fileId(): string
    {
        return (string) $this->orderFile->getFieldData('OXFILEID');
    }

    public function getEshopModel(): OrderFileModel
    {
        return $this->orderFile;
    }

    public static function getModelClass(): string
    {
        return orderFileModel::class;
    }
}
