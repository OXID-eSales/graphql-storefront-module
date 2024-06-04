<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Customer\Service;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\GraphQL\Storefront\Customer\Exception\InvalidEmail;
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
    public function testResetPasswordByUpdateIdSuccessful(): void
    {
        $customer = $this->createStub(User::class);
        $password = 'password';
        $passwordRepeated = 'password';

        $passwordInfrastructure = $this->createMock(PasswordInfrastructureInterface::class);
        $passwordInfrastructure->expects($this->once())
            ->method('validatePassword')
            ->with(
                $customer,
                $password,
                $passwordRepeated
            );
        $customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $customerRepository->expects($this->once())
            ->method('getCustomerByPasswordUpdateHash')
            ->with('1234')
            ->willReturn($customer);
        $customerRepository->expects($this->once())
            ->method('saveNewPasswordForCustomer')
            ->with($customer, $password)
            ->willReturn(true);

        $passwordService = $this->getSut(
            customerRepository: $customerRepository,
            passwordInfrastructure: $passwordInfrastructure
        );

        $passwordService->resetPasswordByUpdateHash('1234', $password, $passwordRepeated);
    }

    public function testResetPasswordByUpdateIdFailing(): void
    {
        $customer = $this->createStub(User::class);
        $password = 'password';
        $passwordRepeated = 'password';

        $passwordInfrastructure = $this->createMock(PasswordInfrastructureInterface::class);
        $passwordInfrastructure->expects($this->once())
            ->method('validatePassword')
            ->with(
                $customer,
                $password,
                $passwordRepeated
            );
        $customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $customerRepository->expects($this->once())
            ->method('getCustomerByPasswordUpdateHash')
            ->with('1234')
            ->willReturn($customer);
        $customerRepository->expects($this->once())
            ->method('saveNewPasswordForCustomer')
            ->with($customer, $password)
            ->willReturn(false);

        $passwordService = $this->getSut(
            customerRepository: $customerRepository,
            passwordInfrastructure: $passwordInfrastructure
        );

        $passwordService->resetPasswordByUpdateHash('1234', $password, $passwordRepeated);
    }


    public function testSendPasswordForgotEmailSuccessfull(): void
    {
        $email = 'test@email.com';

        $passwordInfrastructure = $this->createMock(PasswordInfrastructureInterface::class);
        $passwordInfrastructure->expects($this->once())
            ->method('sendPasswordForgotEmail')
            ->with($email)
            ->willReturn(true);

        $passwordService = $this->getSut(
            passwordInfrastructure: $passwordInfrastructure
        );
        $this->assertTrue($passwordService->sendPasswordForgotEmail($email));
    }

    public function testSendPasswordForgotEmailNoCustomerFound(): void
    {
        $email = 'test@email.com';

        $passwordInfrastructure = $this->createMock(PasswordInfrastructureInterface::class);
        $passwordInfrastructure->expects($this->once())
            ->method('sendPasswordForgotEmail')
            ->with($email)
            ->willReturn(false);

        $passwordService = $this->getSut(
            passwordInfrastructure: $passwordInfrastructure
        );

        $this->expectException(InvalidEmail::class);
        $this->expectExceptionMessage("This e-mail address 'test@email.com' is invalid!");
        $passwordService->sendPasswordForgotEmail($email);
    }

    private function getSut(
        CustomerRepositoryInterface $customerRepository = null,
        CustomerInterface $customerService = null,
        AuthenticationServiceInterface $authenticationService = null,
        PasswordInfrastructureInterface $passwordInfrastructure = null
    ): PasswordService {
        return new PasswordService(
            repository: $customerRepository ?? $this->createStub(CustomerRepositoryInterface::class),
            customerService: $customerService ?? $this->createStub(CustomerInterface::class),
            authenticationService: $authenticationService ?? $this->createStub(AuthenticationServiceInterface::class),
            passwordInfrastructure: $passwordInfrastructure ?? $this->createStub(PasswordInfrastructureInterface::class)
        );
    }
}
