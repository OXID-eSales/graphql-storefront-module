<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Order;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group order
 */
final class CustomerOrderFilesCest extends BaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const OTHER_USERNAME = 'otheruser@oxid-esales.com';

    private const PASSWORD = 'useruser';

    public function testCustomerOrderFiles(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
                customer{
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
                        files{
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
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $customerFiles = $result['data']['customer']['files'];
        $orderFiles    = $result['data']['customer']['orders'][0]['files'];

        $expectedFiles = [
            [
                'file' => [
                    'product' => [
                        'id'     => 'oiaa81b5e002fc2f73b9398c361c0b97',
                        'active' => true,
                        'title'  => 'Online-Shops mit OXID eShop',
                    ],
                    'id'               => 'oiaad7812ae7127283b8fd6d309ea5d5',
                    'filename'         => 'ch03.pdf',
                    'onlyPaidDownload' => false,
                ],
                'id'                          => '729aafa296783575ddfd8e9527355b3b',
                'filename'                    => 'ch03.pdf',
                'firstDownload'               => '2020-09-10T09:14:15+02:00',
                'latestDownload'              => '2020-09-10T09:14:15+02:00',
                'downloadCount'               => 1,
                'maxDownloadCount'            => 0,
                'validUntil'                  => '2020-09-11T09:14:15+02:00',
                'valid'                       => false,
            ],
        ];

        $I->assertMatchesRegularExpression(
            '/https?:\/\/.*\..*sorderfileid=' . $expectedFiles[0]['id'] . '/',
            $customerFiles[0]['url']
        );
        $I->assertMatchesRegularExpression(
            '/https?:\/\/.*\..*sorderfileid=' . $expectedFiles[0]['id'] . '/',
            $orderFiles[0]['url']
        );
        unset($customerFiles[0]['url'], $orderFiles[0]['url']);

        $I->assertEquals($customerFiles, $expectedFiles);
        $I->assertEquals($orderFiles, $expectedFiles);
    }

    public function testCustomerOrderFilesWithNonExistingFile(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::PASSWORD);

        $I->sendGQLQuery(
            'query {
                customer {
                    id
                    orders {
                        id
                        files {
                            id
                            file {
                                id
                            }
                        }
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame([
            'id'   => '886deb7e49bb2e51b4fb939f6ed7655c',
            'file' => null,
        ], $result['data']['customer']['orders'][0]['files'][0]);
    }
}
