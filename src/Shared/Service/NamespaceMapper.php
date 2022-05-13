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
    private const SPACE = '\\OxidEsales\\GraphQL\\Storefront\\';

    public function getControllerNamespaceMapping(): array
    {
        return [
            self::SPACE . 'Product\\Controller' => __DIR__ . '/../../Product/Controller/',
            self::SPACE . 'Vendor\\Controller' => __DIR__ . '/../../Vendor/Controller/',
            self::SPACE . 'Category\\Controller' => __DIR__ . '/../../Category/Controller/',
            self::SPACE . 'Manufacturer\\Controller' => __DIR__ . '/../../Manufacturer/Controller/',
            self::SPACE . 'Link\\Controller' => __DIR__ . '/../../Link/Controller/',
            self::SPACE . 'Action\\Controller' => __DIR__ . '/../../Action/Controller/',
            self::SPACE . 'Banner\\Controller' => __DIR__ . '/../../Banner/Controller/',
            self::SPACE . 'Promotion\\Controller' => __DIR__ . '/../../Promotion/Controller/',
            self::SPACE . 'Content\\Controller' => __DIR__ . '/../../Content/Controller/',
            self::SPACE . 'Currency\\Controller' => __DIR__ . '/../../Currency/Controller/',
            self::SPACE . 'Attribute\\Controller' => __DIR__ . '/../../Attribute/Controller/',
            self::SPACE . 'Review\\Controller' => __DIR__ . '/../../Review/Controller/',
            self::SPACE . 'WishedPrice\\Controller' => __DIR__ . '/../../WishedPrice/Controller/',
            self::SPACE . 'Customer\\Controller' => __DIR__ . '/../../Customer/Controller/',
            self::SPACE . 'NewsletterStatus\\Controller' => __DIR__ . '/../../NewsletterStatus/Controller/',
            self::SPACE . 'Country\\Controller' => __DIR__ . '/../../Country/Controller/',
            self::SPACE . 'Basket\\Controller' => __DIR__ . '/../../Basket/Controller/',
            self::SPACE . 'Contact\\Controller' => __DIR__ . '/../../Contact/Controller/',
            self::SPACE . 'Address\\Controller' => __DIR__ . '/../../Address/Controller/',
            self::SPACE . 'Translation\\Controller' => __DIR__ . '/../../Translation/Controller/',
        ];
    }

    public function getTypeNamespaceMapping(): array
    {
        return [
            self::SPACE . 'Shared\\DataType' => __DIR__ . '/../../Shared/DataType/',
            self::SPACE . 'Shared\\Service' => __DIR__ . '/../../Shared/Service/',
            self::SPACE . 'Product\\DataType' => __DIR__ . '/../../Product/DataType/',
            self::SPACE . 'Product\\Service' => __DIR__ . '/../../Product/Service/',
            self::SPACE . 'Vendor\\DataType' => __DIR__ . '/../../Vendor/DataType/',
            self::SPACE . 'Vendor\\Service' => __DIR__ . '/../../Vendor/Service/',
            self::SPACE . 'Category\\DataType' => __DIR__ . '/../../Category/DataType/',
            self::SPACE . 'Category\\Service' => __DIR__ . '/../../Category/Service/',
            self::SPACE . 'Manufacturer\\DataType' => __DIR__ . '/../../Manufacturer/DataType/',
            self::SPACE . 'Manufacturer\\Service' => __DIR__ . '/../../Manufacturer/Service/',
            self::SPACE . 'Link\\DataType' => __DIR__ . '/../../Link/DataType/',
            self::SPACE . 'Action\\DataType' => __DIR__ . '/../../Action/DataType/',
            self::SPACE . 'Banner\\DataType' => __DIR__ . '/../../Banner/DataType/',
            self::SPACE . 'Banner\\Service' => __DIR__ . '/../../Banner/Service/',
            self::SPACE . 'Promotion\\DataType' => __DIR__ . '/../../Promotion/DataType/',
            self::SPACE . 'Content\\DataType' => __DIR__ . '/../../Content/DataType/',
            self::SPACE . 'Content\\Service' => __DIR__ . '/../../Content/Service/',
            self::SPACE . 'Currency\\DataType' => __DIR__ . '/../../Currency/DataType/',
            self::SPACE . 'Attribute\\DataType' => __DIR__ . '/../../Attribute/DataType/',
            self::SPACE . 'Review\\DataType' => __DIR__ . '/../../Review/DataType/',
            self::SPACE . 'Review\\Service' => __DIR__ . '/../../Review/Service/',
            self::SPACE . 'WishedPrice\\DataType' => __DIR__ . '/../../WishedPrice/DataType/',
            self::SPACE . 'WishedPrice\\Service' => __DIR__ . '/../../WishedPrice/Service/',
            self::SPACE . 'Customer\\DataType' => __DIR__ . '/../../Customer/DataType/',
            self::SPACE . 'Customer\\Service' => __DIR__ . '/../../Customer/Service/',
            self::SPACE . 'NewsletterStatus\\DataType' => __DIR__ . '/../../NewsletterStatus/DataType/',
            self::SPACE . 'NewsletterStatus\\Service' => __DIR__ . '/../../NewsletterStatus/Service/',
            self::SPACE . 'Country\\DataType' => __DIR__ . '/../../Country/DataType/',
            self::SPACE . 'Country\\Service' => __DIR__ . '/../../Country/Service/',
            self::SPACE . 'File\\DataType' => __DIR__ . '/../../File/DataType/',
            self::SPACE . 'File\\Service' => __DIR__ . '/../../File/Service/',
            self::SPACE . 'Contact\\DataType' => __DIR__ . '/../../Contact/DataType/',
            self::SPACE . 'Contact\\Service' => __DIR__ . '/../../Contact/Service/',
            self::SPACE . 'Address\\DataType' => __DIR__ . '/../../Address/DataType/',
            self::SPACE . 'Address\\Service' => __DIR__ . '/../../Address/Service/',
            self::SPACE . 'Order\\DataType' => __DIR__ . '/../../Order/DataType/',
            self::SPACE . 'Order\\Service' => __DIR__ . '/../../Order/Service/',
            self::SPACE . 'Voucher\\DataType' => __DIR__ . '/../../Voucher/DataType/',
            self::SPACE . 'Voucher\\Service' => __DIR__ . '/../../Voucher/Service/',
            self::SPACE . 'Basket\\DataType' => __DIR__ . '/../../Basket/DataType/',
            self::SPACE . 'Basket\\Service' => __DIR__ . '/../../Basket/Service/',
            self::SPACE . 'DeliveryMethod\\DataType' => __DIR__ . '/../../DeliveryMethod/DataType/',
            self::SPACE . 'DeliveryMethod\\Service' => __DIR__ . '/../../DeliveryMethod/Service/',
            self::SPACE . 'Payment\\DataType' => __DIR__ . '/../../Payment/DataType/',
            self::SPACE . 'Translation\\DataType' => __DIR__ . '/../../Translation/DataType/',
        ];
    }
}
