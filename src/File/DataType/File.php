<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\File\DataType;

use OxidEsales\Eshop\Application\Model\File as FileModel;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class File implements ShopModelAwareInterface
{
    /** @var FileModel */
    private $file;

    public function __construct(
        FileModel $file
    ) {
        $this->file = $file;
    }

    /**
     * @Field()
     */
    public function id(): ID
    {
        return new ID($this->file->getId());
    }

    /**
     * @Field()
     */
    public function filename(): string
    {
        return (string)$this->file->getRawFieldData('OXFILENAME');
    }

    /**
     * @Field()
     */
    public function onlyPaidDownload(): bool
    {
        return (bool)$this->file->getRawFieldData('OXPURCHASEDONLY');
    }

    public function getEshopModel(): FileModel
    {
        return $this->file;
    }

    public function productId(): string
    {
        return (string)$this->file->getRawFieldData('OXARTID');
    }

    public static function getModelClass(): string
    {
        return fileModel::class;
    }
}
