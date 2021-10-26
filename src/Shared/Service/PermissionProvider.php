<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Service;

use OxidEsales\GraphQL\Base\Framework\PermissionProviderInterface;

final class PermissionProvider implements PermissionProviderInterface
{
    public function getPermissions(): array
    {
        return [
            'oxidadmin' => [
                'VIEW_INACTIVE_ACTION',
                'VIEW_INACTIVE_ATTRIBUTE',
                'VIEW_INACTIVE_BANNER',
                'VIEW_INACTIVE_CATEGORY',
                'VIEW_INACTIVE_CONTENT',
                'VIEW_ALL_CUSTOMERS',
                'VIEW_INACTIVE_LINK',
                'VIEW_INACTIVE_MANUFACTURER',
                'VIEW_INACTIVE_PRODUCT',
                'VIEW_INACTIVE_PROMOTION',
                'VIEW_INACTIVE_REVIEW',
                'VIEW_INACTIVE_VENDOR',
                'VIEW_USER',
                'VIEW_WISHED_PRICES',
                'DELETE_WISHED_PRICE',
                'VIEW_RATINGS',
                'DELETE_RATING',
                'DELETE_REVIEW',
                'VIEW_INACTIVE_COUNTRY',
                'DELETE_DELIVERY_ADDRESS',
                'DELETE_BASKET',
                'CREATE_BASKET',
                'ADD_PRODUCT_TO_BASKET',
                'REMOVE_BASKET_PRODUCT',
                'ADD_VOUCHER',
                'REMOVE_VOUCHER',
                'PLACE_ORDER',
            ],
            'oxidnotyetordered' => [
                'CREATE_BASKET',
                'ADD_PRODUCT_TO_BASKET',
                'REMOVE_BASKET_PRODUCT',
                'ADD_VOUCHER',
                'REMOVE_VOUCHER',
                'PLACE_ORDER',
            ],
            'oxidcustomer' => [
                'CREATE_BASKET',
                'ADD_PRODUCT_TO_BASKET',
                'REMOVE_BASKET_PRODUCT',
                'ADD_VOUCHER',
                'REMOVE_VOUCHER',
                'PLACE_ORDER',
            ],
        ];
    }
}
