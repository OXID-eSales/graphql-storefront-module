<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\Eshop\Application\Model\Groups;
use OxidEsales\Eshop\Core\Model\BaseModel as ShopBaseModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\GraphQL\Base\Tests\Integration\EnterpriseTestCase;

final class CategoryEnterpriseAccessRightsTest extends EnterpriseTestCase
{
    private const CATEGORY_ID = '30e44ab83fdee7564.23264141'; //Bekleidung

    private const USER_GROUP_ID = '_test_user_group';

    private const USER_TO_GROUP_ID = '_test_user_to_group';

    private const USER_ID = 'e7af1c3b786fd02906ccd75698f4e6b9'; //user@oxid-esales.com

    private const OXOBJECTRIGHTS_ID = '_test_object_rights';

    protected function setUp(): void
    {
        parent::setUp();

        $this->setGETRequestParameter(
            'lang',
            '0'
        );

        $this->getConfig()->setConfigParam('blUseRightsRoles', 3);

        //make sure we have no user and rights are reset
        Registry::getSession()->setUser(null);
        Registry::getSession()->setAdminMode(false);
    }

    /**
     * Tear down.
     */
    protected function tearDown(): void
    {
        $this->cleanUpTable('oxgroups', 'oxid');
        $this->cleanUpTable('oxobjectrights', 'oxid');
        $this->cleanUpTable('oxobject2group', 'oxid');

        parent::tearDown();
    }

    /**
     * Test case that category is fully visible for all users.
     * Anonymus user requests the category.
     * He should be able to see it.
     */
    public function testCategoryVisibleForAllAnonymusUserCanSeeIt(): void
    {
        $result = $this->query(
            'query { category (categoryId: "' . self::CATEGORY_ID . '"){id, title}}'
        );

        $this->assertEquals(
            [
                'id' => self::CATEGORY_ID,
                'title' => 'Bekleidung',
            ],
            $result['body']['data']['category']
        );
    }

    /**
     * Test case that category is fully visible for all users.
     * Anonymus user requests the category.
     * He should be able to see it.
     */
    public function testFilterForCategoryVisibleForAllAnonymusUserCanSeeIt(): void
    {
        $query = 'query {
            categories(filter: {
                title: {
                    equals: "Bekleidung"
                }
            }) {
                id,
                title
            }
        }';

        $result = $this->query($query);

        $this->assertEquals(
            [
                'id' => self::CATEGORY_ID,
                'title' => 'Bekleidung',
            ],
            $result['body']['data']['categories']['0']
        );
    }

    /**
     * Test case that category is exclusively visible for user in group self::USER_GROUP_ID.
     * Anonymus user requests the category.
     * He should not be able to see it.
     */
    public function testCategoryRightsExclusivelyVisibleForGroupAnonymusUserCannotSeeIt(): void
    {
        $this->createUserGroup();
        $this->setCategoryRightsExclusivelyVisibleForGroup();

        $result = $this->query(
            'query { category (categoryId: "' . self::CATEGORY_ID . '"){id, title}}'
        );

        $this->assertSame(
            'Category was not found by id: ' . self::CATEGORY_ID,
            $result['body']['errors'][0]['message']
        );
    }

    /**
     * Test case that category is exclusively visible for user in group self::USER_GROUP_ID.
     * Anonymus user requests the category.
     * He should not be able to see it.
     */
    public function testFilterCategoryRightsExclusivelyVisibleForGroupAnonymusUserCannotSeeIt(): void
    {
        $query = 'query {
            categories(filter: {
                title: {
                    equals: "Bekleidung"
                }
            }) {
                id,
                title
            }
        }';

        $this->createUserGroup();
        $this->setCategoryRightsExclusivelyVisibleForGroup();

        $result = $this->query($query);

        $this->assertEmpty(
            $result['body']['categories']
        );
    }

    /*
     * Test case that category is exclusively visible for user in group self::USER_GROUP_ID.
     * Example user requests the category.
     * He should be able to see it as he is assigned to test group.
     */
    public function testCategoryRightsExclusivelyVisibleForGroupAssignedUserCanSeeIt(): void
    {
        $this->createUserGroup();
        $this->setCategoryRightsExclusivelyVisibleForGroup();
        $this->addUserToGroup(self::USER_ID);

        $result = $this->query(
            'query { token (username: "user@oxid-esales.com", password: "useruser") }'
        );
        $this->setAuthToken($result['body']['data']['token']);

        $result = $this->query(
            'query { category (categoryId: "' . self::CATEGORY_ID . '"){id, title}}'
        );

        $this->assertEquals(
            [
                'id' => self::CATEGORY_ID,
                'title' => 'Bekleidung',
            ],
            $result['body']['data']['category']
        );
    }

    /*
     * Test case that category is exclusively visible for user in group self::USER_GROUP_ID.
     * Example user requests the category.
     * He should be able to see it as he is assigned to test group.
     */
    public function testFilterCategoryRightsExclusivelyVisibleForGroupAssignedUserCanSeeIt(): void
    {
        $this->createUserGroup();
        $this->setCategoryRightsExclusivelyVisibleForGroup();
        $this->addUserToGroup(self::USER_ID);

        $result = $this->query('query { token (username: "user@oxid-esales.com", password: "useruser") }');
        $this->setAuthToken($result['body']['data']['token']);

        $result = $this->query(
            'query {
            categories(filter: {
                title: {
                    equals: "Bekleidung"
                }
            }) {
                id,
                title
            }
        }'
        );

        $this->assertEquals(
            [
                'id' => self::CATEGORY_ID,
                'title' => 'Bekleidung',
            ],
            $result['body']['data']['categories']['0']
        );
    }

    /*
     * Test case that category is exclusively visible for user in group self::USER_GROUP_ID.
     * Example user requests the category with a token.
     * He should not be able to see it as here he is not assigned to test group.
     */
    public function testCategoryRightsExclusivelyVisibleForGroupTokenUserCannotSeeIt(): void
    {
        $this->createUserGroup();
        $this->setCategoryRightsExclusivelyVisibleForGroup();

        $result = $this->query(
            'query { token (username: "user@oxid-esales.com", password: "useruser") }'
        );
        $this->setAuthToken($result['body']['data']['token']);

        $result = $this->query(
            'query { category (categoryId: "' . self::CATEGORY_ID . '"){id, title}}'
        );

        $this->assertSame(
            'Category was not found by id: ' . self::CATEGORY_ID,
            $result['body']['errors'][0]['message']
        );
    }

    private function createUserGroup(): void
    {
        $userGroup = oxNew(Groups::class);
        $userGroup->setId(self::USER_GROUP_ID);
        $userGroup->assign(
            [
                'oxactice' => '1',
                'oxtitle' => 'test group',
            ]
        );
        $userGroup->save();
    }

    private function addUserToGroup(string $userId): void
    {
        $relation = oxNew(ShopBaseModel::class);
        $relation->init('oxobject2group');
        $relation->setId(self::USER_TO_GROUP_ID);

        $relation->assign(
            [
                'oxobjectid' => $userId,
                'oxgroupsid' => self::USER_GROUP_ID,
            ]
        );
        $relation->save();
    }

    private function setCategoryRightsExclusivelyVisibleForGroup(): void
    {
        $this->assignObjectRightsForGroup(self::CATEGORY_ID);
    }

    private function assignObjectRightsForGroup(string $objectId): void
    {
        $objectRights = oxNew(ShopBaseModel::class);
        $objectRights->init('oxobjectrights');
        $objectRights->setId(self::OXOBJECTRIGHTS_ID);
        $objectRights->assign(
            [
                'oxobjectid' => $objectId,
                'oxgroupidx' => 131072,
                'oxoffset' => 0,
                'oxaction' => 1,
            ]
        );
        $objectRights->save();
    }
}
