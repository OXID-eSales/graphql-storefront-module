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
    public function testValidatePasswordWithExpectedParametersThrowsNoExceptions(): void
    {
        $customerStub = $this->createStub(User::class);
        $passwordExample1 = uniqid();
        $passwordExample2 = uniqid();

        $inputValidatorSpy = $this->createMock(InputValidator::class);
        $inputValidatorSpy->expects($this->once())
            ->method('checkPassword')
            ->with($customerStub, $passwordExample1, $passwordExample2, true)
            ->willReturn(null);

        $sut = $this->getSut(inputValidator: $inputValidatorSpy);
        $sut->validatePassword($customerStub, $passwordExample1, $passwordExample2);
    }

    public function testValidatePasswordWithExpectedParametersReturnsExceptionAndRethrowsAsOurs(): void
    {
        $customerStub = $this->createStub(User::class);
        $exceptionMessage = uniqid();
        $passwordExample1 = uniqid();
        $passwordExample2 = uniqid();

        $inputValidatorSpy = $this->createMock(InputValidator::class);
        $inputValidatorSpy->method('checkPassword')
            ->with($customerStub, $passwordExample1, $passwordExample2, true)
            ->willReturn(new StandardException($exceptionMessage));

        $this->expectException(PasswordValidationException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $sut = $this->getSut(inputValidator: $inputValidatorSpy);
        $sut->validatePassword($customerStub, $passwordExample1, $passwordExample2);
    }

    public function testSendPasswordForgotEmailSuccessful(): void
    {
        $email = uniqid();

        $emailServiceStub = $this->createMock(Email::class);
        $emailServiceStub->method('sendForgotPwdEmail')->with($email)->willReturn(true);
        $oxNewFactoryMock = $this->getOxNewFactoryByClass(Email::class, $emailServiceStub);

        $sut = $this->getSut(oxNewFactory: $oxNewFactoryMock);
        $this->assertTrue($sut->sendPasswordForgotEmail($email));
    }

    public function testSendPasswordForgotEmailCustomerNotFound(): void
    {
        $email = uniqid();

        $emailServiceStub = $this->createMock(Email::class);
        $emailServiceStub->method('sendForgotPwdEmail')->with($email)->willReturn(false);
        $oxNewFactoryMock = $this->getOxNewFactoryByClass(Email::class, $emailServiceStub);

        $this->expectException(CustomerEmailNotFound::class);
        $this->expectExceptionMessageMatches("#$email#");

        $sut = $this->getSut(oxNewFactory: $oxNewFactoryMock);
        $this->assertTrue($sut->sendPasswordForgotEmail($email));
    }

    public function testSendPasswordForgotEmailNotSend(): void
    {
        $email = uniqid();

        $emailServiceStub = $this->createMock(Email::class);
        $emailServiceStub->method('sendForgotPwdEmail')->with($email)->willReturn(-1);
        $oxNewFactoryMock = $this->getOxNewFactoryByClass(Email::class, $emailServiceStub);

        $sut = $this->getSut(oxNewFactory: $oxNewFactoryMock);
        $this->assertFalse($sut->sendPasswordForgotEmail($email));
    }

    private function getSut(
        OxNewFactoryInterface $oxNewFactory = null,
        InputValidator $inputValidator = null,
    ): Password {
        return new Password(
            oxNewFactory: $oxNewFactory ?? $this->createStub(OxNewFactoryInterface::class),
            inputValidator: $inputValidator ?? $this->createStub(InputValidator::class)
        );
    }

    private function getOxNewFactoryByClass(string $class, mixed $returnValue): OxNewFactoryInterface
    {
        $oxNewFactoryMock = $this->createMock(OxNewFactoryInterface::class);
        $oxNewFactoryMock->expects($this->once())
            ->method('getModel')
            ->with($class)
            ->willReturn($returnValue);

        return $oxNewFactoryMock;
    }
}
