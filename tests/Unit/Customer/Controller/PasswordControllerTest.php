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

    public function testCustomerPasswordResetSuccessfull(): void
    {
        $passwordService = $this->createMock(PasswordServiceInterface::class);
        $passwordService->expects($this->once())->method('resetPasswordByUpdateId')->with(
            '1234',
            'newPassword',
            'newPassword'
        )->willReturn(true);

        $passwordController = new Password($passwordService);
        $passwordController->customerPasswordReset(
            '1234',
            'newPassword',
            'newPassword'
        );
    }

    public function testCustomerPasswordResetFailing(): void
    {
        $passwordService = $this->createMock(PasswordServiceInterface::class);
        $passwordService->expects($this->once())->method('resetPasswordByUpdateId')->with(
            '1234',
            'newPassword',
            'anotherNewPassword'
        )->willReturn(false);

        $passwordController = new Password($passwordService);
        $passwordController->customerPasswordReset(
            '1234',
            'newPassword',
            'anotherNewPassword'
        );
    }
}
