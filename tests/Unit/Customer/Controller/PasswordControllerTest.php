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
    public function testCustomerPasswordChangeMethodReturnsServiceResult(bool $expectedBoolean): void
    {
        $password1 = uniqid();
        $password2 = uniqid();

        $serviceSpy = $this->createMock(PasswordServiceInterface::class);
        $serviceSpy->expects($this->once())
            ->method('change')
            ->with(
                $password1,
                $password2
            )
            ->willReturn($expectedBoolean);

        $passwordController = new Password(passwordService: $serviceSpy);
        $this->assertSame($expectedBoolean, $passwordController->customerPasswordChange($password1, $password2));
    }

    /** @dataProvider booleanDataProvider */
    public function testCustomerPasswordForgotRequestMethodReturnsServiceResult(bool $expectedBoolean): void
    {
        $exampleEmail = uniqid();

        $serviceSpy = $this->createMock(PasswordServiceInterface::class);
        $serviceSpy->expects($this->once())
            ->method('sendPasswordForgotEmail')
            ->with($exampleEmail)
            ->willReturn($expectedBoolean);

        $passwordController = new Password(passwordService: $serviceSpy);
        $this->assertSame($expectedBoolean, $passwordController->customerPasswordForgotRequest($exampleEmail));
    }

    /** @dataProvider booleanDataProvider */
    public function testCustomerPasswordResetMethodReturnsServiceResult(bool $expectedBoolean): void
    {
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
            ->willReturn($expectedBoolean);

        $passwordController = new Password(passwordService: $serviceSpy);
        $this->assertSame(
            $expectedBoolean,
            $passwordController->customerPasswordReset($exampleHash, $password1, $password2)
        );
    }
}
