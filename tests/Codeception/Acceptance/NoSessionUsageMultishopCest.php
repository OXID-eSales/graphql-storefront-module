<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance;

use Codeception\Util\HttpCode;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group graphql_session_multishop
 */
final class NoSessionUsageMultishopCest extends MultishopBaseCest
{
    private const SUBSHOP_PRODUCT_ID = '_test_product_77'; //shop 2 product

    private const USERNAME = 'user@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const PUBLIC_BASKET = '_test_basket_public'; //belongs to shop 1 user

    public function _after(AcceptanceTester $I): void
    {
        $I->logout();
    }

    public function testSubshopIdFromSessionParameter(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query{
                product(id: "' . self::SUBSHOP_PRODUCT_ID . '") {
                    id
                    title
                }
            }',
            [],
            0,
            2,
            true
        );

        //product does exist in shop 2
        $I->seeResponseCodeIs(HttpCode::OK);

        $I->sendGQLQuery(
            'query{
                product(id: "' . self::SUBSHOP_PRODUCT_ID . '") {
                    id
                    title
                }
            }',
            [],
            0,
            1,
            true
        );

        //product does not exist in shop 1
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);

        //Try again but this time send shop 2 sid but no shp parameter.
        //EE is forced to check session for shp. So in case sid parameter is sent,
        //session would started and subshop id found in 'actshop' session variable.
        //Unless we make sure that graphql does not process requests with sid/force_sid parameter.
        $sid = $this->getSubShopSessionId($I);
        $this->sendQueryWithSidWithoutShopIdParameter(
            $I,
            'query{
                product(id: "' . self::SUBSHOP_PRODUCT_ID . '") {
                    id
                    title
                }
            }',
            $sid
        );

        $I->seeResponseCodeIs(HttpCode::INTERNAL_SERVER_ERROR);

        //Commented lines only work without session fixes in graphql-base module
        //$I->seeResponseCodeIs(HttpCode::OK);
        //$I->seeResponseIsJson();
        //$result = $I->grabJsonResponseAsArray();
        //$I->assertEquals('Product 721', $result['data']['product']['title']);

        //Try again with sending shop 2 sid but no shp parameter.
        //Only that we remove 'actshop' from session before call so we'll end up
        //with shop id 1 (default) and product is not found.
        $this->setShopIdToSession($sid);
        $this->sendQueryWithSidWithoutShopIdParameter(
            $I,
            'query{
                product(id: "' . self::SUBSHOP_PRODUCT_ID . '") {
                    id
                    title
                }
            }',
            $sid
        );

        $I->seeResponseCodeIs(HttpCode::INTERNAL_SERVER_ERROR);
    }

    public function testSubshopIdFromSessionCookie(AcceptanceTester $I): void
    {
        $I->sendGQLQuery(
            'query{
                product(id: "' . self::SUBSHOP_PRODUCT_ID . '") {
                    id
                    title
                }
            }',
            [],
            0,
            1,
            true
        );

        //product does not exist in shop 1
        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);

        //Try again but this time send shop 2 sid as cookie but no shp parameter.
        //EE is forced to check session for shp but as there is not sid request parameter,
        //fallback shop 1 is used.
        $sid = $this->getSubShopSessionId($I);
        $this->sendQueryWithSidCookieWithoutShopIdParameter(
            $I,
            'query{
                product(id: "' . self::SUBSHOP_PRODUCT_ID . '") {
                    id
                    title
                }
            }',
            $sid
        );

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);

        //Session::start() is called during Config::init(). If not sid parameter is
        //sent in request, shop searches for a session cookie. We cannot prevent
        //session cookie usage for graphql, but skipSession parameter will prevent
        //Session start even if sid cookie is present.
    }

    private function getSubShopSessionId(AcceptanceTester $I): string
    {
        $uri = '/index.php?shp=2&lang=0';

        $I->getRest()->sendPOST(
            $uri,
            [
                'shp'     => 2,
                'lang'    => 0,
                'cl'      => 'start',
                'fnc'     => 'login',
                'lgn_usr' => self::USERNAME,
                'lgn_pwd' => self::PASSWORD,
            ]
        );

        $I->seeResponseCodeIs(HttpCode::OK);
        $sid = $I->extractSidFromResponseCookies();
        $I->assertNotEmpty($sid);

        $this->setShopIdToSession($sid, 2);

        return $sid;
    }

    private function setShopIdToSession(string $sid, int $shopId = 0): void
    {
        $shopId = (0 === $shopId) ? null: $shopId;

        //Get 'actshop' variable value 2 (subshop) into this session/remove it.
        session_id($sid);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        Registry::getSession()->setId($sid);
        Registry::getSession()->setVariable('actshop', $shopId);
        Registry::getSession()->freeze();
    }

    private function sendQueryWithSidWithoutShopIdParameter(AcceptanceTester $I, string $query, string $sid): void
    {
        $uri = '/graphql?lang=0&skipSession=false&sid=' . $sid . '&force_sid=' . $sid;

        $rest = $I->getRest();
        $rest->deleteHeader('Cookie');
        $rest->haveHTTPHeader('Content-Type', 'application/json');
        $rest->sendPOST($uri, [
            'query'     => $query,
        ]);
    }

    private function sendQueryWithSidCookieWithoutShopIdParameter(AcceptanceTester $I, string $query, string $sid): void
    {
        $uri = '/graphql?lang=0';

        $rest = $I->getRest();
        $rest->haveHTTPHeader('Cookie', 'sid_key=oxid;sid=' . $sid);
        $rest->haveHTTPHeader('Content-Type', 'application/json');
        $rest->sendPOST($uri, [
            'query'     => $query,
        ]);
    }
}
