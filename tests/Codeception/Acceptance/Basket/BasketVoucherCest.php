<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Basket;

use Codeception\Util\HttpCode;
use GraphQL\Validator\Rules\FieldsOnCorrectType;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group basket
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
                basket(id: "' . self::PRIVATE_BASKET . '") {
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

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $result = $I->grabJsonResponseAsArray();

        [$voucher1, $voucher2] = $result['data']['basket']['vouchers'];

        $expectedSeries1 = [
            'id'           => 'serie2',
            'title'        => 'serie2',
            'description'  => 'serie2 description',
            'validFrom'    => '2000-01-01T00:00:00+01:00',
            'validTo'      => '2050-12-31T00:00:00+01:00',
            'discount'     => 2.0,
            'discountType' => 'absolute',
        ];
        $expectedVoucher1 = [
            'id'       => 'serie2voucher',
            'reserved' => '2020-10-01T13:28:34+02:00',
            'voucher'  => 'serie2voucher',
            'discount' => null,
            'series'   => $expectedSeries1,
        ];

        $expectedSeries2 = [
            'id'           => 'serie3',
            'title'        => 'serie3',
            'description'  => 'serie3 description',
            'validFrom'    => '2000-01-01T00:00:00+01:00',
            'validTo'      => '2050-12-31T00:00:00+01:00',
            'discount'     => 3.0,
            'discountType' => 'absolute',
        ];
        $expectedVoucher2 = [
            'id'       => 'serie3voucher',
            'reserved' => '2020-10-01T13:28:34+02:00',
            'voucher'  => 'serie3voucher',
            'discount' => null,
            'series'   => $expectedSeries2,
        ];

        $I->assertEquals($expectedVoucher1, $voucher1);
        $I->assertEquals($expectedVoucher2, $voucher2);
    }

    public function testGetBasketVouchersNoVouchers(AcceptanceTester $I): void
    {
        $I->login(self::OTHER_USERNAME, self::OTHER_PASSWORD);

        $I->sendGQLQuery(
            'query {
                basket(id: "' . self::PRIVATE_WISHLIST . '") {
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

        $I->seeResponseCodeIs(HttpCode::OK);
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

        $variables = [
            'basketId'      => self::PRIVATE_BASKET,
            'voucherNumber' => 'voucher-number',
        ];

        $mutation = '
            mutation ($basketId: String!, $voucherNumber: String!) {
                basketAddVoucher(basketId: $basketId, voucherNumber: $voucherNumber) {
                    vouchers {
                        number
                    }
                }
            }
        ';

        $I->sendGQLQuery($mutation, $variables);

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $result          = $I->grabJsonResponseAsArray();
        $expectedMessage = FieldsOnCorrectType::undefinedFieldMessage('basketAddVoucher', 'Mutation', [], []);
        $I->assertEquals($expectedMessage, $result['errors'][0]['message']);
    }

    /**
     * @group allowed_to_fail_for_anonymous_token
     */
    public function basketRemoveVoucherWithAnonymousUser(AcceptanceTester $I): void
    {
        $I->login();

        $variables = [
            'basketId'      => self::PRIVATE_BASKET,
            'voucherNumber' => 'voucher-number',
        ];

        $mutation = '
            mutation ($basketId: String!, $voucherNumber: String!) {
                basketRemoveVoucher(basketId: $basketId, voucherNumber: $voucherNumber) {
                    vouchers {
                        number
                    }
                }
            }
        ';

        $I->sendGQLQuery($mutation, $variables);

        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $result          = $I->grabJsonResponseAsArray();
        $expectedMessage = FieldsOnCorrectType::undefinedFieldMessage('basketRemoveVoucher', 'Mutation', [], []);
        $I->assertEquals($expectedMessage, $result['errors'][0]['message']);
    }
}
