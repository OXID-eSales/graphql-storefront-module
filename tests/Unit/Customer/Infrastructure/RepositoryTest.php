<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Customer\Infrastructure;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotFoundByUpdateHash;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\OxNewFactoryInterface;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\RepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Repository
 */
class RepositoryTest extends TestCase
{
    public function testSaveNewPasswordForCustomer(): void
    {
        $newPassword = 'password';

        $customer = $this->createMock(User::class);
        $customer->expects($this->exactly(2))->method('setPassword')->with($newPassword);
        $customer->expects($this->exactly(2))->method('setUpdateKey')->with(true);

        $sharedRepository = $this->createMock(RepositoryInterface::class);
        $sharedRepository->expects($this->exactly(2))
            ->method('saveModel')
            ->with($customer)
            ->willReturn(true, false);
        $oxNewFactory = $this->createStub(OxNewFactoryInterface::class);

        $repository = new Repository($sharedRepository, $oxNewFactory);
        $this->assertTrue($repository->saveNewPasswordForCustomer($customer, $newPassword));
        $this->assertFalse($repository->saveNewPasswordForCustomer($customer, $newPassword));
    }

    public function testGetCustomerByPasswordUpdateId(): void
    {
        $passwordUpdateId = '1234';

        $userMock = $this->createMock(User::class);
        $userMock->expects($this->once())->method('loadUserByUpdateId')->with($passwordUpdateId);
        $userMock->expects($this->once())->method('isLoaded')->willReturn(true);

        $sharedRepository = $this->createMock(RepositoryInterface::class);
        $oxNewFactory = $this->createMock(OxNewFactoryInterface::class);
        $oxNewFactory->expects($this->once())->method('getModel')->with(User::class)->willReturn($userMock);

        $repository = new Repository($sharedRepository, $oxNewFactory);
        $repository->getCustomerByPasswordUpdateHash($passwordUpdateId);
    }

    public function testCustomerByPasswordUpdateIdNotLoaded(): void
    {
        $passwordUpdateId = 'wrongId';

        $userMock = $this->createMock(User::class);
        $userMock->expects($this->once())->method('loadUserByUpdateId')->with($passwordUpdateId);
        $userMock->expects($this->once())->method('isLoaded')->willReturn(false);

        $sharedRepository = $this->createMock(RepositoryInterface::class);
        $oxNewFactory = $this->createMock(OxNewFactoryInterface::class);
        $oxNewFactory->expects($this->once())->method('getModel')->with(User::class)->willReturn($userMock);

        $this->expectException(CustomerNotFoundByUpdateHash::class);
        $this->expectExceptionMessage('No customer was found by update hash: "wrongId".');

        $repository = new Repository($sharedRepository, $oxNewFactory);
        $repository->getCustomerByPasswordUpdateHash($passwordUpdateId);
    }
}
