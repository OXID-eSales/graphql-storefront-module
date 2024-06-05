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
