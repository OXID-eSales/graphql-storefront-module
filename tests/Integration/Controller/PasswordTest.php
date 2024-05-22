<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Controller;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\GraphQL\Storefront\Tests\Integration\BaseTestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @covers OxidEsales\GraphQL\Storefront\Customer\Controller\Password
 */
final class PasswordTest extends BaseTestCase
{
    #[Test]
    public function forgotPasswordEmailRequestWrongEmail(): void
    {
        $result = $this->query('
            mutation {
                customerPasswordForgotRequest(email: "foo@bar.com")
            }
        ');

        $this->assertCount(1, $result['body']['errors']);
        $error = $result['body']['errors'][0];
        $this->assertEquals("This e-mail address 'foo@bar.com' is invalid!", $error['message']);
    }

    #[Test]
    public function forgotPasswordEmailRequest(): void
    {
        $result = $this->query('
            mutation {
                customerPasswordForgotRequest(email: "user@oxid-esales.com")
            }
        ');

        $this->assertTrue($result['body']['data']['customerPasswordForgotRequest']);
    }

    #[Test]
    public function resetPasswordDontMatch(): void
    {
        $result = $this->customerPasswordResetMutation($this->getUpdateId(), 'newpassword', 'repeatpassword');

        $this->assertCount(1, $result['body']['errors']);
        $error = $result['body']['errors'][0];
        $this->assertEquals("Passwords do not match", $error['message']);
    }

    #[Test]
    public function resetPasswordShort(): void
    {
        $result = $this->customerPasswordResetMutation($this->getUpdateId(), 'pass', 'pass');

        $this->assertCount(1, $result['body']['errors']);
        $error = $result['body']['errors'][0];
        $this->assertEquals("Password does not match length requirements", $error['message']);
    }

    #[Test]
    public function resetPasswordEmpty(): void
    {
        $result = $this->customerPasswordResetMutation($this->getUpdateId(), '', '');

        $this->assertCount(1, $result['body']['errors']);
        $error = $result['body']['errors'][0];
        $this->assertEquals("Password does not match length requirements", $error['message']);
    }

    #[Test]
    public function resetPasswordWrongUpdateId(): void
    {
        $result = $this->customerPasswordResetMutation('foobar', 'newpassword', 'newpassword');

        $this->assertFalse($result['body']['data']['customerPasswordReset']);
    }

    #[Test]
    public function resetPassword(): void
    {
        $result = $this->customerPasswordResetMutation($this->getUpdateId(), 'newpassword', 'newpassword');
        $this->assertTrue($result['body']['data']['customerPasswordReset']);

        $result = $this->query('
            query {
                token(username: "user@oxid-esales.com", password: "newpassword")
            }
        ');
        $this->assertNotEmpty($result['body']['data']['token']);
    }

    private function getUpdateId(): string
    {
        $userId = 'e7af1c3b786fd02906ccd75698f4e6b9';
        $user = oxNew(User::class);
        $user->load($userId);
        return $user->getUpdateId();
    }

    private function customerPasswordResetMutation(string $updateId, string $newPassword, string $repeatPassword): array
    {
        return $this->query(sprintf("
            mutation {
                customerPasswordReset(updateId: \"%s\", newPassword: \"%s\", repeatPassword: \"%s\")
            }
        ", $updateId, $newPassword, $repeatPassword));
    }
}
