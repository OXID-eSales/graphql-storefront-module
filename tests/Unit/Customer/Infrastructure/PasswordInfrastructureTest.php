<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Customer\Infrastructure;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\InputValidator;
use OxidEsales\GraphQL\Storefront\Customer\Exception\PasswordValidationException;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Password;
use PHPUnit\Framework\TestCase;

/**
 * @covers \OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Password
 */
class PasswordInfrastructureTest extends TestCase
{
    public function testValidatePasswordValid(): void
    {
        $customer = $this->createStub(User::class);
        $newPassword = 'password';
        $newPasswordRepeated = 'password';
        $inputValidator = $this->createMock(InputValidator::class);
        $inputValidator->expects($this->once())
            ->method('checkPassword')
            ->with($customer, $newPassword, $newPasswordRepeated, true)
            ->willReturn(null);

        $passwordInfrastructure = new Password($inputValidator);
        $passwordInfrastructure->validatePassword($customer, $newPassword, $newPasswordRepeated);
    }

    /**
     * @dataProvider exceptionsDataProvider
     */
    public function testValidatePasswordException(string $message, string $password, string $passwordRepeated): void
    {
        $customer = $this->createStub(User::class);
        $standardException = new StandardException($message);

        $inputValidator = $this->createMock(InputValidator::class);
        $inputValidator->expects($this->once())
            ->method('checkPassword')
            ->with($customer, $password, $passwordRepeated, true)
            ->willReturn($standardException);

        $this->expectException(PasswordValidationException::class);
        $this->expectExceptionMessage($message);

        $passwordInfrastructure = new Password($inputValidator);
        $passwordInfrastructure->validatePassword($customer, $password, $passwordRepeated);
    }

    public static function exceptionsDataProvider(): \Generator
    {
        yield [
            'exceptionClass' => PasswordValidationException::class,
            'exceptionMessage' => 'Please enter a password.',
            'password' => '',
            'passwordRepeated' => ''
        ];

        yield [
            'exceptionClass' => PasswordValidationException::class,
            'exceptionMessage' => 'Error: your password is too short.',
            'password' => 'abc',
            'passwordRepeated' => 'abc'
        ];

        yield [
            'exceptionClass' => PasswordValidationException::class,
            'exceptionMessage' => 'Error: passwords don\'t match.',
            'password' => 'niceAndCoolPassword',
            'passwordRepeated' => 'niceAndCoolPasswort'
        ];
    }

}
