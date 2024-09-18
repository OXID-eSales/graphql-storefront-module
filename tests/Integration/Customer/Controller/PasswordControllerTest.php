<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Customer\Controller;

use Lcobucci\JWT\UnencryptedToken;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\GraphQL\Base\DataType\Login as LoginDatatype;
use OxidEsales\GraphQL\Base\Service\LoginServiceInterface;
use OxidEsales\GraphQL\Storefront\Customer\Controller\Password;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer;
use OxidEsales\GraphQL\Storefront\Customer\Service\PasswordInterface as PasswordServiceInterface;
use OxidEsales\GraphQL\Base\Tests\Integration\TestCase;

/**
 * @covers \OxidEsales\GraphQL\Storefront\Customer\Controller\Password
 */
class PasswordControllerTest extends TestCase
{
    public function testCustomerPasswordChangeMethodReturnsServiceResult(): void
    {
        $password1 = uniqid();
        $password2 = uniqid();

        $user     = oxNew(User::class);
        $customer = new Customer($user);

        $passwordService = $this->createMock(PasswordServiceInterface::class);
        $passwordService->expects($this->once())
            ->method('change')
            ->with(
                $password1,
                $password2
            )
            ->willReturn($customer);

        $refreshToken = uniqid();
        $accessToken = $this->createConfiguredStub(UnencryptedToken::class, [
            'toString' => uniqid()
        ]);

        $loginDataType = new LoginDatatype(
            refreshToken: $refreshToken,
            accessToken: $accessToken
        );

        $loginService = $this->createMock(LoginServiceInterface::class);
        $loginService->expects($this->once())
            ->method('login')
            ->willReturn($loginDataType);

        $passwordController = new Password(
            passwordService: $passwordService,
            loginService: $loginService
        );

        $this->assertSame($loginDataType, $passwordController->customerPasswordChange($password1, $password2));
    }
}
