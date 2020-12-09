<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Address;

use Codeception\Util\HttpCode;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\BaseCest;
use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

/**
 * @group address
 */
final class DeliveryAddressRelationsCest extends BaseCest
{
    private const USERNAME = 'user@oxid-esales.com';

    private const US_USERNAME = 'existinguser@oxid-esales.com';

    private const PASSWORD = 'useruser';

    private const COUNTRY_ID = 'a7c40f631fc920687.20179984'; //Germany

    public function testGetCountryRelation(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->queryCountryRelation($I, 1);
        $I->seeResponseCodeIs(HttpCode::OK);

        $deliveryAddresses = $result['data']['customerDeliveryAddresses'];
        $I->assertEquals(2, count($deliveryAddresses));

        [$deliveryAddress1, $deliveryAddress2] = $deliveryAddresses;
        $I->assertEquals(1, count($deliveryAddress1['country']));
        $I->assertNotEmpty($deliveryAddress1['country']);
        $I->assertSame('Germany', $deliveryAddress1['country']['title']);

        $I->assertEquals(1, count($deliveryAddress2['country']));
        $I->assertNotEmpty($deliveryAddress2['country']);
        $I->assertSame('Austria', $deliveryAddress2['country']['title']);
    }

    public function testGetInactiveCountryRelation(AcceptanceTester $I): void
    {
        $this->setCountryActiveStatus(self::COUNTRY_ID, 0);
        $I->login(self::USERNAME, self::PASSWORD);

        $this->queryCountryRelation($I);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);

        $this->setCountryActiveStatus(self::COUNTRY_ID, 1);
    }

    public function testGetInactiveCountryRelationAsAdmin(AcceptanceTester $I): void
    {
        $this->setCountryActiveStatus(self::COUNTRY_ID, 0);
        $I->login('admin', 'admin');

        $this->queryCountryRelation($I);

        $I->seeResponseCodeIs(HttpCode::OK);

        $this->setCountryActiveStatus(self::COUNTRY_ID, 1);
    }

    public function testGetStateRelation(AcceptanceTester $I): void
    {
        $I->login(self::US_USERNAME, self::PASSWORD);

        $result = $this->queryStateRelation($I, 1);

        $I->seeResponseCodeIs(HttpCode::OK);

        $deliveryAddresses = $result['data']['customerDeliveryAddresses'];
        $I->assertCount(1, $deliveryAddresses);

        [$deliveryAddress] = $deliveryAddresses;

        $I->assertNotEmpty($deliveryAddress['state']);
        $I->assertSame('Arizona', $deliveryAddress['state']['title']);
    }

    public function testGetStateRelationAsNull(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->queryStateRelation($I);

        $I->seeResponseCodeIs(HttpCode::OK);

        $deliveryAddresses = $result['data']['customerDeliveryAddresses'];
        $I->assertEquals(2, count($deliveryAddresses));

        [$deliveryAddress] = $deliveryAddresses;
        $I->assertNull($deliveryAddress['state']);
    }

    private function queryCountryRelation(AcceptanceTester $I, int $languageId = 0): array
    {
        $I->sendGQLQuery(
            'query {
                customerDeliveryAddresses {
                    country {
                        title
                    }
                }
            }',
            null,
            $languageId
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }

    private function setCountryActiveStatus(string $countryId, int $active): void
    {
        $queryBuilder = ContainerFactory::getInstance()
            ->getContainer()
            ->get(QueryBuilderFactoryInterface::class)
            ->create();

        $queryBuilder
            ->update('oxcountry')
            ->set('oxactive', $active)
            ->where('OXID = :OXID')
            ->setParameter(':OXID', $countryId)
            ->execute();
    }

    private function queryStateRelation(AcceptanceTester $I, int $languageId = 0): array
    {
        $I->sendGQLQuery(
            'query {
                customerDeliveryAddresses {
                    state {
                        title
                    }
                }
            }',
            null,
            $languageId
        );

        $I->seeResponseIsJson();

        return $I->grabJsonResponseAsArray();
    }
}
