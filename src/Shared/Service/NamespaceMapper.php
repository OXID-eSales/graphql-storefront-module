<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Service;

use OxidEsales\GraphQL\Base\Framework\NamespaceMapperInterface;

final class NamespaceMapper implements NamespaceMapperInterface
{
    public function getControllerNamespaceMapping(): array
    {
        return [
            '\\OxidEsales\\GraphQL\\Storefront\\Controller'               => __DIR__ . '/../../Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Product\\Controller'      => __DIR__ . '/../../Product/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Vendor\\Controller'       => __DIR__ . '/../../Vendor/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Category\\Controller'     => __DIR__ . '/../../Category/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Manufacturer\\Controller' => __DIR__ . '/../../Manufacturer/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Link\\Controller'         => __DIR__ . '/../../Link/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Review\\Controller'       => __DIR__ . '/../../Review/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Action\\Controller'       => __DIR__ . '/../../Action/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Banner\\Controller'       => __DIR__ . '/../../Banner/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Promotion\\Controller'    => __DIR__ . '/../../Promotion/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Content\\Controller'      => __DIR__ . '/../../Content/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Currency\\Controller'     => __DIR__ . '/../../Currency/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Attribute\\Controller'    => __DIR__ . '/../../Attribute/Controller/',
        ];
    }

    public function getTypeNamespaceMapping(): array
    {
        return [
            '\\OxidEsales\\GraphQL\\Storefront\\DataType'                  => __DIR__ . '/../../DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Shared\\DataType'          => __DIR__ . '/../../Shared/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Shared\\Service'           => __DIR__ . '/../../Shared/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Product\\DataType'         => __DIR__ . '/../../Product/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Product\\Service'          => __DIR__ . '/../../Product/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Product\\Infrastructure'   => __DIR__ . '/../../Product/Infrastructure/',
            '\\OxidEsales\\GraphQL\\Storefront\\Vendor\\DataType'          => __DIR__ . '/../../Vendor/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Vendor\\Service'           => __DIR__ . '/../../Vendor/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Category\\DataType'        => __DIR__ . '/../../Category/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Category\\Service'         => __DIR__ . '/../../Category/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Manufacturer\\DataType'    => __DIR__ . '/../../Manufacturer/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Manufacturer\\Service'     => __DIR__ . '/../../Manufacturer/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Link\\DataType'            => __DIR__ . '/../../Link/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Review\\DataType'          => __DIR__ . '/../../Review/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Review\\Service'           => __DIR__ . '/../../Review/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Action\\DataType'          => __DIR__ . '/../../Action/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Banner\\DataType'          => __DIR__ . '/../../Banner/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Banner\\Service'           => __DIR__ . '/../../Banner/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Banner\\Infrastructure'    => __DIR__ . '/../../Banner/Infrastructure/',
            '\\OxidEsales\\GraphQL\\Storefront\\Promotion\\DataType'       => __DIR__ . '/../../Promotion/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Promotion\\Infrastructure' => __DIR__ . '/../../Promotion/Infrastructure/',
            '\\OxidEsales\\GraphQL\\Storefront\\Content\\DataType'         => __DIR__ . '/../../Content/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Content\\Service'          => __DIR__ . '/../../Content/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Currency\\DataType'        => __DIR__ . '/../../Currency/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Attribute\\DataType'       => __DIR__ . '/../../Attribute/DataType/',
        ];
    }
}
