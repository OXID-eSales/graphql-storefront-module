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
    public static function booleanDataProvider(): \Generator
    {
        yield [
            'expectedBoolean' => true
        ];

        yield [
            'expectedBoolean' => false
        ];
    }

    /** @dataProvider booleanDataProvider */
    public function testSaveNewPasswordResultBasedOnSaveModel(bool $expectedBoolean): void
    {
        $customerStub = $this->createStub(User::class);

        $sharedRepositorySpy = $this->createMock(RepositoryInterface::class);
        $sharedRepositorySpy->expects($this->once())
            ->method('saveModel')
            ->with($customerStub)
            ->willReturn($expectedBoolean);

        $sut = new Repository(
            repository: $sharedRepositorySpy,
            oxNewFactory: $this->createStub(OxNewFactoryInterface::class)
        );

        $this->assertSame($expectedBoolean, $sut->saveNewPasswordForCustomer($customerStub, 'anything'));
    }

    public function testGetCustomerByPasswordUpdateHashNotLoaded(): void
    {
        $failedToLoadUserStub = $this->createMock(User::class);
        $failedToLoadUserStub->method('isLoaded')->willReturn(false);

        $sut = new Repository(
            repository: $this->createMock(RepositoryInterface::class),
            oxNewFactory: $oxNewFactoryStub = $this->createMock(OxNewFactoryInterface::class)
        );
        $oxNewFactoryStub->method('getModel')->with(User::class)->willReturn($failedToLoadUserStub);

        $passwordUpdateHash = uniqid();
        $this->expectException(CustomerNotFoundByUpdateHash::class);
        $this->expectExceptionMessageMatches("#$passwordUpdateHash#");

        $sut->getCustomerByPasswordUpdateHash($passwordUpdateHash);
    }
}
