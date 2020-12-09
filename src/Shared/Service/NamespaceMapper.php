<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Shared\Service;

use OxidEsales\GraphQL\Base\Framework\NamespaceMapperInterface;

final class NamespaceMapper implements NamespaceMapperInterface
{
    public function getControllerNamespaceMapping(): array
    {
        return [
            '\\OxidEsales\\GraphQL\\Catalogue\\Controller'               => __DIR__ . '/../../Controller/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Product\\Controller'      => __DIR__ . '/../../Product/Controller/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Vendor\\Controller'       => __DIR__ . '/../../Vendor/Controller/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Category\\Controller'     => __DIR__ . '/../../Category/Controller/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Manufacturer\\Controller' => __DIR__ . '/../../Manufacturer/Controller/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Link\\Controller'         => __DIR__ . '/../../Link/Controller/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Review\\Controller'       => __DIR__ . '/../../Review/Controller/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Action\\Controller'       => __DIR__ . '/../../Action/Controller/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Banner\\Controller'       => __DIR__ . '/../../Banner/Controller/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Promotion\\Controller'    => __DIR__ . '/../../Promotion/Controller/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Content\\Controller'      => __DIR__ . '/../../Content/Controller/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Currency\\Controller'     => __DIR__ . '/../../Currency/Controller/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Attribute\\Controller'    => __DIR__ . '/../../Attribute/Controller/',
        ];
    }

    public function getTypeNamespaceMapping(): array
    {
        return [
            '\\OxidEsales\\GraphQL\\Catalogue\\DataType'                  => __DIR__ . '/../../DataType/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Shared\\DataType'          => __DIR__ . '/../../Shared/DataType/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Shared\\Service'           => __DIR__ . '/../../Shared/Service/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Product\\DataType'         => __DIR__ . '/../../Product/DataType/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Product\\Service'          => __DIR__ . '/../../Product/Service/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Product\\Infrastructure'   => __DIR__ . '/../../Product/Infrastructure/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Vendor\\DataType'          => __DIR__ . '/../../Vendor/DataType/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Vendor\\Service'           => __DIR__ . '/../../Vendor/Service/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Category\\DataType'        => __DIR__ . '/../../Category/DataType/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Category\\Service'         => __DIR__ . '/../../Category/Service/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Manufacturer\\DataType'    => __DIR__ . '/../../Manufacturer/DataType/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Manufacturer\\Service'     => __DIR__ . '/../../Manufacturer/Service/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Link\\DataType'            => __DIR__ . '/../../Link/DataType/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Review\\DataType'          => __DIR__ . '/../../Review/DataType/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Review\\Service'           => __DIR__ . '/../../Review/Service/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Action\\DataType'          => __DIR__ . '/../../Action/DataType/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Banner\\DataType'          => __DIR__ . '/../../Banner/DataType/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Banner\\Service'           => __DIR__ . '/../../Banner/Service/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Banner\\Infrastructure'    => __DIR__ . '/../../Banner/Infrastructure/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Promotion\\DataType'       => __DIR__ . '/../../Promotion/DataType/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Promotion\\Infrastructure' => __DIR__ . '/../../Promotion/Infrastructure/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Content\\DataType'         => __DIR__ . '/../../Content/DataType/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Content\\Service'          => __DIR__ . '/../../Content/Service/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Currency\\DataType'        => __DIR__ . '/../../Currency/DataType/',
            '\\OxidEsales\\GraphQL\\Catalogue\\Attribute\\DataType'       => __DIR__ . '/../../Attribute/DataType/',
        ];
    }
}
