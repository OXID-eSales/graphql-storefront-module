<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Customer\Service;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\PasswordInterface as PasswordInfrastructureInterface;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\RepositoryInterface as CustomerRepositoryInterface;
use OxidEsales\GraphQL\Storefront\Customer\Service\CustomerInterface;
use OxidEsales\GraphQL\Storefront\Customer\Service\Password as PasswordService;
use PHPUnit\Framework\TestCase;
use TheCodingMachine\GraphQLite\Security\AuthenticationServiceInterface;

/**
 * @covers \OxidEsales\GraphQL\Storefront\Customer\Service\Password
 */
class PasswordServiceTest extends TestCase
{

    public function testResetPasswordByUpdateId(): void
    {
        $customer = $this->createStub(User::class);
        $password = 'password';
        $passwordRepeated = 'password';

        $customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $customerRepository->expects($this->once())
            ->method('getCustomerByPasswordUpdateId')
            ->with('1234')
            ->willReturn($customer);
        $customerRepository->expects($this->once())
            ->method('saveNewPasswordForCustomer')
            ->with($customer, $password);
        $passwordInfrastructure = $this->createMock(PasswordInfrastructureInterface::class);
        $passwordInfrastructure->expects($this->once())
            ->method('validatePassword')
            ->with(
                $customer,
                $password,
                $passwordRepeated
            );
        $customerService = $this->createMock(CustomerInterface::class);
        $authenticationService = $this->createMock(AuthenticationServiceInterface::class);
        $passwordService = new PasswordService(
            $customerRepository,
            $customerService,
            $authenticationService,
            $passwordInfrastructure
        );

        $passwordService->resetPasswordByUpdateId('1234', $password, $passwordRepeated);
    }
}
