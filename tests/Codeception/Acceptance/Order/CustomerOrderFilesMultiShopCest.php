<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Order;

use Codeception\Util\HttpCode;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group order
 */
final class CustomerOrderFilesMultiShopCest extends MultishopBaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    public function testCustomerOrderFilesSubShopOnly(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', false, 'bool', [1, 2]);

        $I->login(self::USERNAME, self::PASSWORD, 2);

        $I->sendGQLQuery(
            'query {
                customer {
                    files {
                        file {
                            product {
                                id
                                active
                                title
                            }
                            id
                            filename
                            onlyPaidDownload
                        }
                        id
                        filename
                        firstDownload
                        latestDownload
                        downloadCount
                        maxDownloadCount
                        validUntil
                        valid
                        url
                    }
                    orders {
                        id
                        files {
                            file {
                                product {
                                    id
                                    active
                                    title
                                }
                                id
                                filename
                                onlyPaidDownload
                            }
                            id
                            filename
                            firstDownload
                            latestDownload
                            downloadCount
                            maxDownloadCount
                            validUntil
                            valid
                            url
                        }
                    }
                }
            }',
            null,
            0,
            2
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $customerFiles = $result['data']['customer']['files'];
        $orderFiles    = $result['data']['customer']['orders'][0]['files'];

        $expectedFiles = [
            [
                'file'             => [
                    'product'          => [
                        'id'     => '_test_product_for_basket',
                        'active' => true,
                        'title'  => 'Product 621',
                    ],
                    'id'               => '48d949cb0af6076f841aea5cb5b703ed',
                    'filename'         => 'ch99.pdf',
                    'onlyPaidDownload' => true,
                ],
                'id'               => '729aafa296783575ddfd8e9527355b9b',
                'filename'         => 'ch99.pdf',
                'firstDownload'    => '2020-09-10T09:14:15+02:00',
                'latestDownload'   => '2020-09-10T09:14:15+02:00',
                'downloadCount'    => 1,
                'maxDownloadCount' => 0,
                'validUntil'       => '2020-09-11T09:14:15+02:00',
                'valid'            => false,
            ],
        ];

        $I->assertRegExp('/https?:\/\/.*\..*sorderfileid=' . $expectedFiles[0]['id'] . '/', $customerFiles[0]['url']);
        $I->assertRegExp('/https?:\/\/.*\..*sorderfileid=' . $expectedFiles[0]['id'] . '/', $orderFiles[0]['url']);
        unset($customerFiles[0]['url'], $orderFiles[0]['url']);

        $I->assertEquals($customerFiles, $expectedFiles);
        $I->assertEquals($orderFiles, $expectedFiles);
    }
}
