<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Customer\Controller;

use OxidEsales\GraphQL\Storefront\Customer\Controller\Password;
use OxidEsales\GraphQL\Storefront\Customer\Service\PasswordInterface as PasswordServiceInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \OxidEsales\GraphQL\Storefront\Customer\Controller\Password
 */
class PasswordControllerTest extends TestCase
{
    public function testCustomerPasswordChangeMethodReturnsServiceResult(): void
    {
        $expectedReturn = (bool)random_int(0, 1);
        $password1 = uniqid();
        $password2 = uniqid();

        $serviceSpy = $this->createMock(PasswordServiceInterface::class);
        $serviceSpy->expects($this->once())
            ->method('change')
            ->with(
                $password1,
                $password2
            )
            ->willReturn($expectedReturn);

        $passwordController = new Password(passwordService: $serviceSpy);
        $this->assertSame($expectedReturn, $passwordController->customerPasswordChange($password1, $password2));
    }

    public function testCustomerPasswordForgotRequestMethodReturnsServiceResult(): void
    {
        $expectedReturn = (bool)random_int(0, 1);
        $exampleEmail = uniqid();

        $serviceSpy = $this->createMock(PasswordServiceInterface::class);
        $serviceSpy->expects($this->once())
            ->method('sendPasswordForgotEmail')
            ->with($exampleEmail)
            ->willReturn($expectedReturn);

        $passwordController = new Password(passwordService: $serviceSpy);
        $this->assertSame($expectedReturn, $passwordController->customerPasswordForgotRequest($exampleEmail));
    }

    public function testCustomerPasswordResetMethodReturnsServiceResult(): void
    {
        $expectedReturn = (bool)random_int(0, 1);
        $exampleHash = uniqid();
        $password1 = uniqid();
        $password2 = uniqid();

        $serviceSpy = $this->createMock(PasswordServiceInterface::class);
        $serviceSpy->expects($this->once())
            ->method('resetPasswordByUpdateHash')
            ->with(
                $exampleHash,
                $password1,
                $password2
            )
            ->willReturn($expectedReturn);

        $passwordController = new Password(passwordService: $serviceSpy);
        $this->assertSame(
            $expectedReturn,
            $passwordController->customerPasswordReset($exampleHash, $password1, $password2)
        );
    }
}
