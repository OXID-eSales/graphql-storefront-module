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
    public function testResetPasswordByUpdateHashSuccessful(): void
    {
        $customerStub = $this->createStub(User::class);
        $password = uniqid();
        $passwordRepeated = uniqid();

        $passwordInfrastructureSpy = $this->createMock(PasswordInfrastructureInterface::class);
        $passwordInfrastructureSpy->expects($this->once())
            ->method('validatePassword')
            ->with(
                $customerStub,
                $password,
                $passwordRepeated
            );

        $expectedSaveResult = (bool)random_int(0, 1);
        $exampleHash = uniqid();
        $customerRepositoryMock = $this->createMock(CustomerRepositoryInterface::class);
        $customerRepositoryMock->method('getCustomerByPasswordUpdateHash')
            ->with($exampleHash)
            ->willReturn($customerStub);
        $customerRepositoryMock->method('saveNewPasswordForCustomer')
            ->with($customerStub, $password)
            ->willReturn($expectedSaveResult);

        $sut = $this->getSut(
            customerRepository: $customerRepositoryMock,
            passwordInfrastructure: $passwordInfrastructureSpy
        );

        $this->assertSame(
            $expectedSaveResult,
            $sut->resetPasswordByUpdateHash($exampleHash, $password, $passwordRepeated)
        );
    }

    public function testSendPasswordForgotEmailSuccessfull(): void
    {
        $email = uniqid();

        $passwordInfrastructureStub = $this->createMock(PasswordInfrastructureInterface::class);
        $passwordInfrastructureStub->method('sendPasswordForgotEmail')
            ->with($email)
            ->willReturn(true);

        $sut = $this->getSut(
            passwordInfrastructure: $passwordInfrastructureStub
        );
        $this->assertTrue($sut->sendPasswordForgotEmail($email));
    }

    public function testSendPasswordForgotEmailNoCustomerFound(): void
    {
        $email = uniqid();

        $passwordInfrastructureStub = $this->createMock(PasswordInfrastructureInterface::class);
        $passwordInfrastructureStub->method('sendPasswordForgotEmail')->willReturn(false);

        $sut = $this->getSut(
            passwordInfrastructure: $passwordInfrastructureStub
        );

        $this->expectException(InvalidEmail::class);
        $this->expectExceptionMessageMatches("#$email#");

        $sut->sendPasswordForgotEmail($email);
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
