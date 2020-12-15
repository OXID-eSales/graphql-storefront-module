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
final class InvoiceAddressRelationsCest extends BaseCest
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

        $invoiceAddress = $result['data']['customerInvoiceAddress'];
        $I->assertEquals(1, count($invoiceAddress['country']));
        $I->assertNotEmpty($invoiceAddress['country']);
        $I->assertSame('Germany', $invoiceAddress['country']['title']);
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

    public function testStateRelation(AcceptanceTester $I): void
    {
        $I->login(self::US_USERNAME, self::PASSWORD);

        $result = $this->queryStateRelation($I, 1);

        $I->seeResponseCodeIs(HttpCode::OK);

        $invoiceAddress = $result['data']['customerInvoiceAddress'];
        $I->assertNotEmpty($invoiceAddress['state']);
        $I->assertSame('Arizona', $invoiceAddress['state']['title']);
    }

    public function testGetStateRelationAsNull(AcceptanceTester $I): void
    {
        $I->login(self::USERNAME, self::PASSWORD);

        $result = $this->queryStateRelation($I);

        $I->seeResponseCodeIs(HttpCode::OK);

        $invoiceAddress = $result['data']['customerInvoiceAddress'];
        $I->assertNull($invoiceAddress['state']);
    }

    private function queryCountryRelation(AcceptanceTester $I, int $languageId = 0): array
    {
        $I->sendGQLQuery(
            'query {
                customerInvoiceAddress {
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
                customerInvoiceAddress {
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
