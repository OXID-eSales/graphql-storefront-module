<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;
use TheCodingMachine\GraphQLite\Middlewares\MissingAuthorizationException;

/**
 * @group basket
 * @group oe_graphql_storefront
 */
final class BasketVoucherCest extends BaseCest
{
    // Private basket
    private const PRIVATE_BASKET = '_test_basket_private';

    private const OTHER_USERNAME = 'otheruser@oxid-esales.com';

    private const OTHER_PASSWORD = 'useruser';

    private const PRIVATE_WISHLIST = '_test_wish_list_private';

    public function testGetBasketVouchers(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);

        $I->sendGQLQuery(
            'query {
                basket(basketId: "' . self::PRIVATE_BASKET . '") {
                    vouchers {
                        id
                        reserved
                        voucher
                        discount
                        series {
                            id
                            title
                            description
                            validFrom
                            validTo
                            discount
                            discountType
                        }
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        [$voucher1, $voucher2] = $result['data']['basket']['vouchers'];

        $this->checkReservationDate($I, $voucher1['reserved']);
        $this->checkReservationDate($I, $voucher2['reserved']);

        //reservation date is checked sepparately
        unset($voucher1['reserved'], $voucher2['reserved']);

        $expectedSeries1 = [
            'id' => 'serie2',
            'title' => 'serie2',
            'description' => 'serie2 description',
            'validFrom' => '2000-01-01T00:00:00+01:00',
            'validTo' => '2050-12-31T00:00:00+01:00',
            'discount' => 2.0,
            'discountType' => 'absolute',
        ];
        $expectedVoucher1 = [
            'id' => 'serie2voucher',
            'voucher' => 'serie2voucher',
            'discount' => null,
            'series' => $expectedSeries1,
        ];

        $expectedSeries2 = [
            'id' => 'serie3',
            'title' => 'serie3',
            'description' => 'serie3 description',
            'validFrom' => '2000-01-01T00:00:00+01:00',
            'validTo' => '2050-12-31T00:00:00+01:00',
            'discount' => 3.0,
            'discountType' => 'absolute',
        ];
        $expectedVoucher2 = [
            'id' => 'serie3voucher',
            'voucher' => 'serie3voucher',
            'discount' => null,
            'series' => $expectedSeries2,
        ];

        $I->assertEquals($expectedVoucher1, $voucher1);
        $I->assertEquals($expectedVoucher2, $voucher2);
    }

    public function testGetBasketVouchersNoVouchers(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);

        $I->sendGQLQuery(
            'query {
                basket(basketId: "' . self::PRIVATE_WISHLIST . '") {
                    vouchers {
                        id
                        reserved
                        voucher
                        discount
                        series {
                            id
                            title
                            description
                            validFrom
                            validTo
                            discount
                            discountType
                        }
                    }
                }
            }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEmpty($result['data']['basket']['vouchers']);
    }

    /**
     * @group allowed_to_fail_for_anonymous_token
     */
    public function basketAddVoucherWithAnonymousUser(AcceptanceTester $I): void
    {
        $I->login();

        $I->sendGQLQuery(
            'mutation {
            basketAddVoucher(basketId: "' . self::PRIVATE_BASKET . '", voucherNumber: "voucher-number") {
                vouchers {
                    number
                }
            }
        }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            MissingAuthorizationException::forbidden()->getMessage(),
            $result['errors'][0]['message']
        );
    }

    /**
     * @group allowed_to_fail_for_anonymous_token
     */
    public function basketRemoveVoucherWithAnonymousUser(AcceptanceTester $I): void
    {
        $I->login();

        $I->sendGQLQuery(
            'mutation{
            basketRemoveVoucher(basketId: "' . self::PRIVATE_BASKET . '", voucherId: "voucher-number") {
                vouchers {
                    number
                }
            }
        }'
        );

        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        $I->assertEquals(
            MissingAuthorizationException::forbidden()->getMessage(),
            $result['errors'][0]['message']
        );
    }

    /**
     * Reservation date is refreshed on basket operation,
     * to avoid race condition we only check if the date is correct.
     */
    private function checkReservationDate(AcceptanceTester $I, string $actualReservationdate): void
    {
        $expectedReservationdate = DateTimeImmutableFactory::fromString('now')->format('Y-m-d');
        $I->assertStringContainsString($expectedReservationdate, $actualReservationdate);
    }
}
