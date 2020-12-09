<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\Service;

use OxidEsales\GraphQL\Storefront\File\DataType\File as FileDataType;
use OxidEsales\GraphQL\Storefront\File\Exception\FileNotFound;
use OxidEsales\GraphQL\Storefront\File\Service\File as FileService;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderFile;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=OrderFile::class)
 */
final class OrderFileRelations
{
    /** @var FileService */
    private $fileService;

    public function __construct(
        FileService $fileService
    ) {
        $this->fileService = $fileService;
    }

    /**
     * @Field()
     */
    public function file(OrderFile $orderFile): ?FileDataType
    {
        try {
            return $this->fileService->file((string) $orderFile->fileId());
        } catch (FileNotFound $e) {
            return null;
        }
    }
}
