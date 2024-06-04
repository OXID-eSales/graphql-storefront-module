<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Customer\Infrastructure;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Application\Model\User as EshopModelUser;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Tests\Integration\BaseTestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @covers \OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Repository
 */
final class PasswordTest extends BaseTestCase
{
    #[Test]
    public function testChangePassword(): void
    {
        $customer = $this->createTestUserWithUpdateKey('coolUpdateKey_123');

        /** @var Repository $customerRepository */
        $customerRepository = $this->get(Repository::class);
        $customerRepository->saveNewPasswordForCustomer($customer, 'newPassword');

        $customerAfterSave = oxNew(User::class);
        $customerAfterSave->load($customer->getId());
        $this->assertTrue(
            password_verify('newPassword', $customerAfterSave->getFieldData('oxpassword'))
        );
        $this->assertEmpty($customerAfterSave->getFieldData('oxupdateKey'));
    }

    #[Test]
    public function testGetCustomerByPasswordUpdateId(): void
    {
        $customer = $this->createTestUserWithUpdateKey('blabla');

        /** @var Repository $customerRepository */
        $customerRepository = $this->get(Repository::class);
        $updateHash = md5($customer->getId().$customer->getShopId().'blabla');
        $customerByUpdateId = $customerRepository->getCustomerByPasswordUpdateHash($updateHash);
        $this->assertEquals($customer->getId(), $customerByUpdateId->getId());
    }

    private function createTestUserWithUpdateKey(string $passwordUpdateKey): EshopModelUser
    {
        $user = oxNew(EshopModelUser::class);
        $user->setId('_testuserid');
        $user->assign([
            'oxusername' => '',
            'oxpassword' => '',
            'oxregister' => '',
            'oxupdateexp' => time()+60,
            'oxupdatekey' => $passwordUpdateKey
        ]);
        $user->save();

        return $user;
    }
}
