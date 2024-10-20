<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Customer\Infrastructure;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Application\Model\User as EshopModelUser;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\RepositoryInterface;
use OxidEsales\GraphQL\Storefront\Tests\Integration\BaseTestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @covers \OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Repository
 */
final class RepositoryTest extends BaseTestCase
{
    #[Test]
    public function testChangePassword(): void
    {
        $customer = $this->createTestUserWithUpdateKey('coolUpdateKey_123');

        /** @var RepositoryInterface $sut */
        $sut = $this->get(RepositoryInterface::class);
        $sut->saveNewPasswordForCustomer($customer, 'newPassword');

        $customerAfterSave = oxNew(User::class);
        $customerAfterSave->load($customer->getId());
        $this->assertTrue(
            password_verify('newPassword', $customerAfterSave->getFieldData('oxpassword'))
        );
        $this->assertEmpty($customerAfterSave->getFieldData('oxupdateKey'));
    }

    #[Test]
    public function testGetCustomerByPasswordUpdateHashReturnsUser(): void
    {
        $updateId = 'foobar';
        $customer = $this->createTestUserWithUpdateKey($updateId);

        /** @var RepositoryInterface $sut */
        $sut = $this->get(RepositoryInterface::class);
        $updateHash = md5($customer->getId() . $customer->getShopId() . $updateId);
        $customerByUpdateId = $sut->getCustomerByPasswordUpdateHash($updateHash);

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
            'oxupdateexp' => time() + 60,
            'oxupdatekey' => $passwordUpdateKey
        ]);
        $user->save();

        return $user;
    }
}
