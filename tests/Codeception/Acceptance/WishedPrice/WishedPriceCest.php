<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\WishedPrice;

use Codeception\Example;
use Codeception\Util\HttpCode;
use OxidEsales\Eshop\Application\Model\PriceAlarm;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group wishedpricefoo
 */
final class WishedPriceCest extends BaseCest
{
    private const ADMIN_USERNAME = 'admin';

    private const ADMIN_PASSWORD = 'admin';

    private const USERNAME = 'user@oxid-esales.com';

    private const FIRSTNAME = 'Marc';

    private const PASSWORD = 'useruser';

    private const WISHED_PRICE = '_test_wished_price_1_'; // Belongs to user@oxid-esales.com

    private const WISHED_PRICE_2 = '_test_wished_price_6_'; // Belongs to user@oxid-esales.com

    private const WISHED_PRICE_WITH_INACTIVE_PRODUCT = '_test_wished_price_4_';

    private const WISHED_PRICE_WITH_NON_EXISTING_PRODUCT = '_test_wished_price_5_';

    private const WISHED_PRICE_WITH_DISABLED_WISHED_PRICE_FOR_PRODUCT = '_test_wished_price_3_';

    private const WISHED_PRICE_WITHOUT_USER = '_test_wished_price_without_user_';

    private const WISHED_PRICE_ASSIGNED_TO_OTHER_USER = '_test_wished_price_2_'; // Belongs to otheruser@oxid-esales.com

    private const WISHED_PRICE_WITH_NON_EXISTING_USER = '_test_wished_price_7_';

    private const WISHED_PRICE_TO_BE_DELETED = '_test_wished_price_delete_';

    private const PRODUCT_ID = '058e613db53d782adfc9f2ccb43c45fe';

    public function _after(AcceptanceTester $I): void
    {
        $this->setShopOrderMail($I);
    }

    public function testGetWishedPrice(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);
        $I->sendGQLQuery('query{
            wishedPrice(id: "' . self::WISHED_PRICE . '") {
                product {
                    title
                }
                price {
                    price
                }
                currency {
                    name
                }
                id
                email
                notificationDate
                creationDate
                inquirer {
                    firstName
                }
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $result      = $I->grabJsonResponseAsArray();
        $wishedPrice = $result['data']['wishedPrice'];

        $I->assertEquals($wishedPrice['product']['title'], 'Kuyichi Ledergürtel JEVER');
        $I->assertEquals($wishedPrice['price']['price'], '10.0');
        $I->assertEquals($wishedPrice['currency']['name'], 'EUR');
        $I->assertEquals($wishedPrice['id'], self::WISHED_PRICE);
        $I->assertEquals($wishedPrice['email'], self::USERNAME);
        $I->assertEquals($wishedPrice['inquirer']['firstName'], self::FIRSTNAME);
        $I->assertNull($wishedPrice['notificationDate']);

        $I->assertEmpty(array_diff(array_keys($wishedPrice), [
            'product',
            'price',
            'currency',
            'id',
            'email',
            'notificationDate',
            'creationDate',
            'inquirer',
        ]));
    }

    public function testGetWishedPriceNotificationDate(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);
        $I->sendGQLQuery('query{
            wishedPrice(id: "' . self::WISHED_PRICE_2 . '") {
                notificationDate
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $result      = $I->grabJsonResponseAsArray();
        $wishedPrice = $result['data']['wishedPrice'];

        $I->assertNotNull($wishedPrice['notificationDate']);
    }

    public function testGetWishedPriceWithoutToken(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query{
            wishedPrice(id: "' . self::WISHED_PRICE . '") {
                id
            }
        }');

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /**
     * @dataProvider dataProviderWishedPrices404and401
     */
    public function testWishedPricesWithResponse404and401(AcceptanceTester $I, Example $data): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery('query{
            wishedPrice(id: "' . $data['id'] . '") {
                id
            }
        }');

        $I->seeResponseCodeIs($data['status']);
    }

    public function dataProviderWishedPrices404and401()
    {
        return [
            [
                'id'     => self::WISHED_PRICE_WITHOUT_USER,
                'status' => HttpCode::UNAUTHORIZED,
            ],
            [
                'id'     => self::WISHED_PRICE_ASSIGNED_TO_OTHER_USER,
                'status' => HttpCode::UNAUTHORIZED,
            ],
            [
                'id'     => self::WISHED_PRICE_WITH_INACTIVE_PRODUCT,
                'status' => HttpCode::UNAUTHORIZED,
            ],
            [
                'id'     => self::WISHED_PRICE_WITH_DISABLED_WISHED_PRICE_FOR_PRODUCT,
                'status' => HttpCode::NOT_FOUND,
            ],
            [
                'id'     => self::WISHED_PRICE_WITH_NON_EXISTING_PRODUCT,
                'status' => HttpCode::NOT_FOUND,
            ],
            [
                'id'     => self::WISHED_PRICE_WITH_NON_EXISTING_USER,
                'status' => HttpCode::UNAUTHORIZED,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderWishedPricesWithAuthorization
     */
    public function testWishedPricesWithAuthorization(AcceptanceTester $I, Example $data): void
    {
        $I->login(self::ADMIN_USERNAME, self::ADMIN_PASSWORD);

        $I->sendGQLQuery('query{
            wishedPrice(id: "' . $data['id'] . '") {
                id
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
    }

    public function dataProviderWishedPricesWithAuthorization()
    {
        return [
            [
                'id' => self::WISHED_PRICE_WITHOUT_USER,
            ],
            [
                'id' => self::WISHED_PRICE_ASSIGNED_TO_OTHER_USER,
            ],
            [
                'id' => self::WISHED_PRICE_WITH_INACTIVE_PRODUCT,
            ],
            [
                'id' => self::WISHED_PRICE_WITH_DISABLED_WISHED_PRICE_FOR_PRODUCT,
            ],
            [
                'id' => self::WISHED_PRICE_WITH_NON_EXISTING_USER,
            ],
        ];
    }

    public function testDeleteWishedPriceWithoutToken(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('mutation {
            wishedPriceDelete(id: "' . self::WISHED_PRICE_TO_BE_DELETED . '")
        }');

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @dataProvider providerDeleteWishedPrice
     */
    public function testDeleteWishedPriceWithToken(AcceptanceTester $I, Example $data): void
    {
        $I->login($data['username'], $data['password']);

        $I->sendGQLQuery('mutation {
            wishedPriceDelete(id: "' . $data['oxid'] . '")
        }');

        $I->seeResponseCodeIs($data['expected']);

        if (HttpCode::OK === $data['expected']) {
            $result = $I->grabJsonResponseAsArray();
            $I->assertTrue($result['data']['wishedPriceDelete']);
        }
    }

    /**
     * @dataProvider providerDeleteWishedPrice
     */
    public function testDeleteNonExistingWishedPrice(AcceptanceTester $I, Example $data): void
    {
        $I->login($data['username'], $data['password']);

        $I->sendGQLQuery('mutation {
            wishedPriceDelete(id: "non_existing_wished_price")
        }');

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    public function providerDeleteWishedPrice()
    {
        return [
            'admin' => [
                'username' => 'admin',
                'password' => 'admin',
                'oxid'     => self::WISHED_PRICE_TO_BE_DELETED . '1_',
                'expected' => 200,
            ],
            'user' => [
                'username' => 'user@oxid-esales.com',
                'password' => 'useruser',
                'oxid'     => self::WISHED_PRICE_TO_BE_DELETED . '2_',
                'expected' => 200,
            ],
            'otheruser' => [
                'username' => 'otheruser@oxid-esales.com',
                'password' => 'useruser',
                'oxid'     => self::WISHED_PRICE_TO_BE_DELETED . '3_',
                'expected' => 401,
            ],
        ];
    }

    public function testWishedPrices401WithoutToken(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('query {
            wishedPrices {
                id
            }
        }');

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    /**
     * @dataProvider providerWishedPrices
     */
    public function testWishedPrices(AcceptanceTester $I, Example $data): void
    {
        $I->login($data['username'], $data['password']);

        $I->sendGQLQuery('query {
            wishedPrices {
                id
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $result = $I->grabJsonResponseAsArray();

        $I->assertCount(
            $data['count'],
            $result['data']['wishedPrices']
        );
    }

    public function providerWishedPrices()
    {
        return [
            'admin' => [
                'username' => 'admin',
                'password' => 'admin',
                'count'    => 0,
            ],
            'user' => [
                'username' => 'user@oxid-esales.com',
                'password' => 'useruser',
                'count'    => 7,
            ],
        ];
    }

    public function testWishedPriceSetWithoutAuthorization(AcceptanceTester $I): void
    {
        $I->sendGQLQuery('mutation {
            wishedPriceSet(wishedPrice: {
                productId: "' . self::PRODUCT_ID . '",
                currencyName: "EUR",
                price: 15.00
            }) {
                id
                email
                notificationDate
                creationDate
            }
        }');

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    /**
     * @dataProvider wishedPriceSetWithMissingEntitiesProvider
     */
    public function testWishedPriceSetWithMissingEntities(AcceptanceTester $I, Example $data): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery('mutation {
            wishedPriceSet(wishedPrice: { productId: "' . $data['productId'] . '", currencyName: "' .
            $data['currency'] . '", price: ' . $data['price'] . '}) {
                id
                email
                notificationDate
                creationDate
            }
        }');

        $I->seeResponseCodeIs($data['status']);
        $I->seeResponseIsJson();

        $result = $I->grabJsonResponseAsArray();
        $I->assertEquals($data['message'], $result['errors'][0]['message']);
    }

    public function wishedPriceSetWithMissingEntitiesProvider(): array
    {
        return [
            'not_existing_product' => [
                'productId' => 'DOES-NOT-EXIST',
                'currency'  => 'EUR',
                'price'     => '15.0',
                'message'   => 'Product was not found by id: DOES-NOT-EXIST',
                'status'    => HttpCode::NOT_FOUND,
            ],
            'not_existing_currency' => [
                'productId' => self::PRODUCT_ID,
                'currency'  => 'ABC',
                'price'     => '15.0',
                'message'   => 'Currency "ABC" was not found',
                'status'    => HttpCode::NOT_FOUND,
            ],
            'wished_price_disabled' => [
                'productId' => self::WISHED_PRICE_WITH_DISABLED_WISHED_PRICE_FOR_PRODUCT,
                'currency'  => 'EUR',
                'price'     => '15.0',
                'message'   => 'Product was not found by id: ' . self::WISHED_PRICE_WITH_DISABLED_WISHED_PRICE_FOR_PRODUCT,
                'status'    => HttpCode::NOT_FOUND,
            ],
            'invalid_price' => [
                'productId' => self::PRODUCT_ID,
                'currency'  => 'EUR',
                'price'     => 'this_is_not_a_vald_price',
                'message'   => 'Field "wishedPriceSet" argument "wishedPrice" requires type Float!, found this_is_not_a_vald_price.',
                'status'    => HttpCode::BAD_REQUEST,
            ],
            'negative_price' => [
                'productId' => self::PRODUCT_ID,
                'currency'  => 'EUR',
                'price'     => -123,
                'message'   => 'Wished price must be positive, was: -123',
                'status'    => HttpCode::BAD_REQUEST,
            ],
        ];
    }

    public function testWishedPriceSet(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery('mutation {
            wishedPriceSet(wishedPrice: {
                productId: "' . self::PRODUCT_ID . '",
                currencyName: "EUR",
                price: 15.00
            }) {
                id
                inquirer {
                    firstName
                }
                email
                product {
                    id
                }
                currency {
                    name
                }
            }
        }');

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $result = $I->grabJsonResponseAsArray();

        $wishedPrice   = $result['data']['wishedPriceSet'];
        $wishedPriceId = $wishedPrice['id'];
        unset($wishedPrice['id']);

        $expectedWishedPrice = [
            'inquirer' => ['firstName' => self::FIRSTNAME],
            'email'    => self::USERNAME,
            'product'  => ['id' => self::PRODUCT_ID],
            'currency' => ['name' => 'EUR'],
        ];

        $I->assertEquals($expectedWishedPrice, $wishedPrice);

        /** @var PriceAlarm $savedWishedPrice */
        $savedWishedPrice = oxNew(PriceAlarm::class);
        $savedWishedPrice->load($wishedPriceId);

        $I->assertTrue($savedWishedPrice->isLoaded());

        $I->assertEquals($expectedWishedPrice['product']['id'], $savedWishedPrice->getFieldData('OXARTID'));
        $I->assertEquals($expectedWishedPrice['currency']['name'], $savedWishedPrice->getFieldData('OXCURRENCY'));
    }

    public function testWishedPriceSetFailsToSendNotification(AcceptanceTester $I): void
    {
        $this->setShopOrderMail($I, '');
        $I->login(self::USERNAME, self::PASSWORD);

        $I->sendGQLQuery('mutation {
            wishedPriceSet(wishedPrice: {
                productId: "' . self::PRODUCT_ID . '",
                currencyName: "EUR",
                price: 15.00
            }) {
                id
            }
        }');

        $I->seeResponseCodeIs(HttpCode::INTERNAL_SERVER_ERROR);
        $I->seeResponseIsJson();

        $result = $I->grabJsonResponseAsArray();
        $I->assertContains(
            'Failed to send notification: Invalid address:  (to):',
            $result['errors']['0']['message']
        );
    }

    private function setShopOrderMail(AcceptanceTester $I, string $value = 'reply@myoxideshop.com'): void
    {
        $I->updateInDatabase(
            'oxshops',
            [
                'oxorderemail' => $value,
            ],
            [
                'oxid' => 1,
            ]
        );
    }
}
