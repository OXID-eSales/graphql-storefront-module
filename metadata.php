<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = [
    'id'            => 'oe_graphql_storefront',
    'title'         => 'GraphQL Storefront',
    'description'   => 'OXID GraphQL Storefront',
    'thumbnail'   => 'out/pictures/logo.png',
    'version'     => '3.0.0',
    'author'      => 'OXID eSales',
    'url'         => 'https://github.com/OXID-eSales/graphql-storefront-module',
    'email'       => 'info@oxid-esales.com',
    'extend'      => [
        \OxidEsales\Eshop\Application\Model\User::class => \OxidEsales\GraphQL\Storefront\Shared\Shop\User::class,
        \OxidEsales\Eshop\Application\Model\Basket::class => \OxidEsales\GraphQL\Storefront\Shared\Shop\Basket::class,
        \OxidEsales\Eshop\Application\Model\Voucher::class => \OxidEsales\GraphQL\Storefront\Shared\Shop\Voucher::class,
        \OxidEsales\Eshop\Core\Language::class => \OxidEsales\GraphQL\Storefront\Shared\Shop\Language::class,
    ],
    'controllers' => [
    ],
    'templates'   => [
    ],
    'blocks'      => [
    ],
    'settings'    => [
    ],
];
