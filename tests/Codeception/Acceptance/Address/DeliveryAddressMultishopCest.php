<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Address;

use Codeception\Example;
use Codeception\Scenario;
use Codeception\Util\HttpCode;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\MultishopBaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group address
 */
final class DeliveryAddressMultiShopCest extends MultishopBaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const DELIVERY_ADDRESS_SHOP_1 = 'test_delivery_address';

    private const DELIVERY_ADDRESS_SHOP_2 = 'test_delivery_address_shop_2';

    private const DELETE_DELIVERY_ADDRESS_SHOP_1 = '_delete_delivery_address';

    private const DELETE_DELIVERY_ADDRESS_SHOP_2 = '_delete_delivery_address_2';

    private const OTHER_USERNAME = 'otheruser@oxid-esales.com';

    private const DELETE_USERNAME = 'multishopuser@oxid-esales.com';

    private const OTHER_PASSWORD = 'useruser';

    public function _before(AcceptanceTester $I, Scenario $scenario): void
    {
        parent::_before($I, $scenario);

        $I->updateConfigInDatabaseForShops('blMallUsers', false, 'bool', [1, 2]);
    }

    /**
     * @dataProvider dataProviderDeliveryAddressPerShop
     */
    public function testAddDeliveryAddressPerShopForMallUser(AcceptanceTester $I, Example $data): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);

        $shopId = $data['shopId'];
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD, $shopId);

        $result = $this->executeMutation($I, $shopId);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->assertNotEmpty($result['data']['customerDeliveryAddressAdd']['id']);
    }

    /**
     * @dataProvider deliveryAddressesDataProviderPerShop
     */
    public function testGetDeliveryAddressesForLoggedInUser(AcceptanceTester $I, Example $data): void
    {
        $shopId   = $data['shopId'];
        $expected = $data['expected'];

        $I->login(self::USERNAME, self::PASSWORD, $shopId);

        $I->sendGQLQuery(
            'query {
                customerDeliveryAddresses {
                    id
                    firstName
                    street
                    streetNumber
                }
            }',
            null,
            0,
            $shopId
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertSame(
            $expected,
            $result['data']['customerDeliveryAddresses']
        );
    }

    public function deliveryAddressDeletionProvider(): array
    {
        return [
            'set1' => [
                'shopId'    => 1,
                'addressId' => self::DELETE_DELIVERY_ADDRESS_SHOP_1,
            ],
            'set2' => [
                'shopId'    => 2,
                'addressId' => self::DELETE_DELIVERY_ADDRESS_SHOP_2,
            ],
        ];
    }

    /**
     * @dataProvider deliveryAddressDeletionProvider
     */
    public function testDeliveryAddressDeletionPerShop(AcceptanceTester $I, Example $data): void
    {
        $shopId            = $data['shopId'];
        $deliveryAddressId = $data['addressId'];

        //subshop user has same username but different oxid
        $I->login(self::DELETE_USERNAME, self::PASSWORD, $shopId);

        $this->deleteCustomerDeliveryAddressMutation($I, $deliveryAddressId, $shopId);

        $I->seeResponseCodeIs(HttpCode::OK);
    }

    /**
     * @dataProvider deliveryAddressDeletionPerDifferentShopProvider
     */
    public function testDeliveryAddressDeletionFromShop1ToShop2(AcceptanceTester $I, Example $data): void
    {
        $shopId            = $data['shopId'];
        $deliveryAddressId = $data['addressId'];

        $I->login(self::USERNAME, self::PASSWORD, $shopId);

        $this->deleteCustomerDeliveryAddressMutation($I, $deliveryAddressId, $shopId);

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    public function testDeliveryAddressDeletionFromOtherSubshopForMallUser(AcceptanceTester $I): void
    {
        $I->updateConfigInDatabaseForShops('blMallUsers', true, 'bool', [1, 2]);

        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD, 1);

        $result    = $this->executeMutation($I, 1);
        $addressId = $result['data']['customerDeliveryAddressAdd']['id'];

        $I->logout();
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD, 2);

        $this->deleteCustomerDeliveryAddressMutation($I, $addressId, 2);

        $I->seeResponseCodeIs(HttpCode::OK);
    }

    protected function dataProviderDeliveryAddressPerShop()
    {
        return [
            'shop_1' => [
                'shopId'   => 1,
            ],
            'shop_2' => [
                'shopId'   => 2,
            ],
        ];
    }

    protected function deliveryAddressesDataProviderPerShop()
    {
        return [
            'shop_1' => [
                'shopId'   => 1,
                'expected' => [
                    [
                        'id'           => self::DELIVERY_ADDRESS_SHOP_1,
                        'firstName'    => 'Marc',
                        'street'       => 'Hauptstr',
                        'streetNumber' => '13',
                    ],
                    [
                        'id'           => 'test_delivery_address_2',
                        'firstName'    => 'Marc',
                        'street'       => 'Hauptstr2',
                        'streetNumber' => '132',
                    ],
                ],
            ],
            'shop_2' => [
                'shopId'   => 2,
                'expected' => [
                    [
                        'id'           => self::DELIVERY_ADDRESS_SHOP_2,
                        'firstName'    => 'Marc2',
                        'street'       => 'Hauptstr2',
                        'streetNumber' => '2',
                    ],
                ],
            ],
        ];
    }

    protected function deliveryAddressDeletionPerDifferentShopProvider(): array
    {
        return [
            'set1' => [
                'shopId'    => 1,
                'addressId' => self::DELETE_DELIVERY_ADDRESS_SHOP_2,
            ],
            'set2' => [
                'shopId'    => 2,
                'addressId' => self::DELETE_DELIVERY_ADDRESS_SHOP_1, ],
        ];
    }

    private function executeMutation(AcceptanceTester $I, int $shopId): array
    {
        $inputFields =  [
            'salutation'     => 'MR',
            'firstName'      => 'Marc',
            'lastName'       => 'Muster',
            'company'        => 'No GmbH',
            'additionalInfo' => 'private delivery',
            'street'         => 'Bertoldstrasse',
            'streetNumber'   => '48',
            'zipCode'        => '79098',
            'city'           => 'Freiburg',
            'countryId'      => 'a7c40f631fc920687.20179984',
            'phone'          => '1234',
            'fax'            => '4321',
        ];
        $queryPart = '';

        foreach ($inputFields as $key => $value) {
            $queryPart .= $key . ': "' . $value . '",' . PHP_EOL;
        }

        $I->sendGQLQuery(
            'mutation {
                customerDeliveryAddressAdd(deliveryAddress: {' .
            $queryPart .
            '})
                {
                    id
                    salutation
                    firstName
                    lastName
                    company
                    additionalInfo
                    street
                    streetNumber
                    zipCode
                    city
                    phone
                    fax
                    country {
                        id
                    }
                }
            }',
            null,
            0,
            $shopId
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function deleteCustomerDeliveryAddressMutation(AcceptanceTester $I, string $deliveryAddressId, int $shopId): array
    {
        $I->sendGQLQuery(
            'mutation {
                customerDeliveryAddressDelete(id: "' . $deliveryAddressId . '")
            }',
            null,
            0,
            $shopId
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }
}
