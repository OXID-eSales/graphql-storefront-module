<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Customer\Infrastructure;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Email;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\InputValidator;
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerEmailNotFound;
use OxidEsales\GraphQL\Storefront\Customer\Exception\PasswordValidationException;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Password;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\OxNewFactoryInterface;
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

        $passwordInfrastructure = $this->getSut(inputValidator: $inputValidator);
        $passwordInfrastructure->validatePassword($customer, $newPassword, $newPasswordRepeated);
    }

    /**
     * @dataProvider exceptionsDataProvider
     */
    public function testValidatePasswordException(string $exceptionMessage, string $password, string $passwordRepeated): void
    {
        $customer = $this->createStub(User::class);
        $standardException = new StandardException($exceptionMessage);

        $inputValidator = $this->createMock(InputValidator::class);
        $inputValidator->expects($this->once())
            ->method('checkPassword')
            ->with($customer, $password, $passwordRepeated, true)
            ->willReturn($standardException);

        $this->expectException(PasswordValidationException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $passwordInfrastructure = $this->getSut(inputValidator: $inputValidator);
        $passwordInfrastructure->validatePassword($customer, $password, $passwordRepeated);
    }

    public function testSendPasswordForgotEmailSuccessful(): void
    {
        $email = 'test@email.com';

        $emailService = $this->createMock(Email::class);
        $emailService->expects($this->once())
            ->method('sendForgotPwdEmail')
            ->with($email)
            ->willReturn(true);
        $oxNewFactory = $this->getOxNewFactoryByClass(Email::class, $emailService);

        $passwordInfrastructure = $this->getSut(oxNewFactory: $oxNewFactory);

        $this->assertTrue($passwordInfrastructure->sendPasswordForgotEmail($email));
    }

    public function testSendPasswordForgotEmailCustomerNotFound(): void
    {
        $email = 'test@email.com';

        $emailService = $this->createMock(Email::class);
        $emailService->expects($this->once())
            ->method('sendForgotPwdEmail')
            ->with($email)
            ->willReturn(false);
        $oxNewFactory = $this->getOxNewFactoryByClass(Email::class, $emailService);

        $passwordInfrastructure = $this->getSut(oxNewFactory: $oxNewFactory);

        $this->expectException(CustomerEmailNotFound::class);
        $this->expectExceptionMessage('Customer was not found for: test@email.com');
        $this->assertTrue($passwordInfrastructure->sendPasswordForgotEmail($email));
    }

    public function testSendPasswordForgotEmailNotSend(): void
    {
        $email = 'test@email.com';

        $emailService = $this->createMock(Email::class);
        $emailService->expects($this->once())
            ->method('sendForgotPwdEmail')
            ->with($email)
            ->willReturn(-1);
        $oxNewFactory = $this->getOxNewFactoryByClass(Email::class, $emailService);

        $passwordInfrastructure = $this->getSut(oxNewFactory: $oxNewFactory);

        $this->assertFalse($passwordInfrastructure->sendPasswordForgotEmail($email));
    }

    public static function exceptionsDataProvider(): \Generator
    {
        yield [
            'exceptionMessage' => 'Please enter a password.',
            'password' => '',
            'passwordRepeated' => ''
        ];

        yield [
            'exceptionMessage' => 'Error: your password is too short.',
            'password' => 'abc',
            'passwordRepeated' => 'abc'
        ];

        yield [
            'exceptionMessage' => 'Error: passwords don\'t match.',
            'password' => 'niceAndCoolPassword',
            'passwordRepeated' => 'niceAndCoolPasswort'
        ];
    }

    private function getSut(
        OxNewFactoryInterface $oxNewFactory = null,
        InputValidator $inputValidator = null,
    ): Password
    {
        return new Password(
            oxNewFactory: $oxNewFactory ?? $this->createStub(OxNewFactoryInterface::class),
            inputValidator: $inputValidator ?? $this->createStub(InputValidator::class)
        );
    }

    private function getOxNewFactoryByClass(string $class, mixed $returnValue): OxNewFactoryInterface
    {
        $oxNewFactory = $this->createMock(OxNewFactoryInterface::class);
        $oxNewFactory->expects($this->once())
            ->method('getModel')
            ->with($class)
            ->willReturn($returnValue);
        return $oxNewFactory;
    }
}
