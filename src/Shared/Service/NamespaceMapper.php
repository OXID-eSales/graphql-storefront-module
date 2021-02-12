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
            '\\OxidEsales\\GraphQL\\Storefront\\Controller'                        => __DIR__ . '/../../Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Product\\Controller'               => __DIR__ . '/../../Product/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Vendor\\Controller'                => __DIR__ . '/../../Vendor/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Category\\Controller'              => __DIR__ . '/../../Category/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Manufacturer\\Controller'          => __DIR__ . '/../../Manufacturer/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Link\\Controller'                  => __DIR__ . '/../../Link/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Action\\Controller'                => __DIR__ . '/../../Action/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Banner\\Controller'                => __DIR__ . '/../../Banner/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Promotion\\Controller'             => __DIR__ . '/../../Promotion/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Content\\Controller'               => __DIR__ . '/../../Content/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Currency\\Controller'              => __DIR__ . '/../../Currency/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Attribute\\Controller'             => __DIR__ . '/../../Attribute/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Rating\\Controller'                => __DIR__ . '/../../Rating/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Review\\Controller'                => __DIR__ . '/../../Review/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\WishedPrice\\Controller'           => __DIR__ . '/../../WishedPrice/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Customer\\Controller'              => __DIR__ . '/../../Customer/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\NewsletterStatus\\Controller'      => __DIR__ . '/../../NewsletterStatus/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Country\\Controller'               => __DIR__ . '/../../Country/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Basket\\Controller'                => __DIR__ . '/../../Basket/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Contact\\Controller'               => __DIR__ . '/../../Contact/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Address\\Controller'               => __DIR__ . '/../../Address/Controller/',
            '\\OxidEsales\\GraphQL\\Storefront\\Translation\\Controller'           => __DIR__ . '/../../Translation/Controller/',

        ];
    }

    public function getTypeNamespaceMapping(): array
    {
        return [
            '\\OxidEsales\\GraphQL\\Storefront\\DataType'                          => __DIR__ . '/../../DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Shared\\DataType'                  => __DIR__ . '/../../Shared/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Shared\\Service'                   => __DIR__ . '/../../Shared/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Product\\DataType'                 => __DIR__ . '/../../Product/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Product\\Service'                  => __DIR__ . '/../../Product/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Product\\Infrastructure'           => __DIR__ . '/../../Product/Infrastructure/',
            '\\OxidEsales\\GraphQL\\Storefront\\Vendor\\DataType'                  => __DIR__ . '/../../Vendor/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Vendor\\Service'                   => __DIR__ . '/../../Vendor/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Category\\DataType'                => __DIR__ . '/../../Category/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Category\\Service'                 => __DIR__ . '/../../Category/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Manufacturer\\DataType'            => __DIR__ . '/../../Manufacturer/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Manufacturer\\Service'             => __DIR__ . '/../../Manufacturer/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Link\\DataType'                    => __DIR__ . '/../../Link/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Action\\DataType'                  => __DIR__ . '/../../Action/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Banner\\DataType'                  => __DIR__ . '/../../Banner/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Banner\\Service'                   => __DIR__ . '/../../Banner/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Banner\\Infrastructure'            => __DIR__ . '/../../Banner/Infrastructure/',
            '\\OxidEsales\\GraphQL\\Storefront\\Promotion\\DataType'               => __DIR__ . '/../../Promotion/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Promotion\\Infrastructure'         => __DIR__ . '/../../Promotion/Infrastructure/',
            '\\OxidEsales\\GraphQL\\Storefront\\Content\\DataType'                 => __DIR__ . '/../../Content/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Content\\Service'                  => __DIR__ . '/../../Content/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Currency\\DataType'                => __DIR__ . '/../../Currency/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Attribute\\DataType'               => __DIR__ . '/../../Attribute/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Rating\\DataType'                  => __DIR__ . '/../../Rating/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Rating\\Service'                   => __DIR__ . '/../../Rating/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Review\\DataType'                  => __DIR__ . '/../../Review/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Review\\Service'                   => __DIR__ . '/../../Review/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\WishedPrice\\DataType'             => __DIR__ . '/../../WishedPrice/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\WishedPrice\\Service'              => __DIR__ . '/../../WishedPrice/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Customer\\DataType'                => __DIR__ . '/../../Customer/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Customer\\Service'                 => __DIR__ . '/../../Customer/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Customer\\Infrastructure'          => __DIR__ . '/../../Customer/Infrastructure/',
            '\\OxidEsales\\GraphQL\\Storefront\\NewsletterStatus\\DataType'        => __DIR__ . '/../../NewsletterStatus/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\NewsletterStatus\\Service'         => __DIR__ . '/../../NewsletterStatus/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\NewsletterStatus\\Infrastructure'  => __DIR__ . '/../../NewsletterStatus/Infrastructure/',
            '\\OxidEsales\\GraphQL\\Storefront\\Country\\DataType'                 => __DIR__ . '/../../Country/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Country\\Service'                  => __DIR__ . '/../../Country/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\File\\DataType'                    => __DIR__ . '/../../File/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\File\\Service'                     => __DIR__ . '/../../File/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Payment\\Service'                  => __DIR__ . '/../../Payment/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Contact\\DataType'                 => __DIR__ . '/../../Contact/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Contact\\Service'                  => __DIR__ . '/../../Contact/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Contact\\Infrastructure'           => __DIR__ . '/../../Contact/Infrastructure/',
            '\\OxidEsales\\GraphQL\\Storefront\\Address\\DataType'                 => __DIR__ . '/../../Address/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Address\\Service'                  => __DIR__ . '/../../Address/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Address\\Infrastructure'           => __DIR__ . '/../../Address/Infrastructure/',
            '\\OxidEsales\\GraphQL\\Storefront\\Order\\DataType'                   => __DIR__ . '/../../Order/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Order\\Service'                    => __DIR__ . '/../../Order/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Order\\Infrastructure'             => __DIR__ . '/../../Order/Infrastructure/',
            '\\OxidEsales\\GraphQL\\Storefront\\Voucher\\DataType'                 => __DIR__ . '/../../Voucher/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Voucher\\Service'                  => __DIR__ . '/../../Voucher/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Basket\\DataType'                  => __DIR__ . '/../../Basket/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Basket\\Service'                   => __DIR__ . '/../../Basket/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Basket\\Infrastructure'            => __DIR__ . '/../../Basket/Infrastructure/',
            '\\OxidEsales\\GraphQL\\Storefront\\DeliveryMethod\\DataType'          => __DIR__ . '/../../DeliveryMethod/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Payment\\DataType'                 => __DIR__ . '/../../Payment/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\DeliveryMethod\\Service'           => __DIR__ . '/../../DeliveryMethod/Service/',
            '\\OxidEsales\\GraphQL\\Storefront\\Translation\\DataType'             => __DIR__ . '/../../Translation/DataType/',
            '\\OxidEsales\\GraphQL\\Storefront\\Translation\\Infrastructure'       => __DIR__ . '/../../Translation/Infrastructure/',
            '\\OxidEsales\\GraphQL\\Storefront\\Translation\\Service'              => __DIR__ . '/../../Translation/Service/',
        ];
    }
}
