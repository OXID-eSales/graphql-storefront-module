<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Infrastructure;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Email;
use OxidEsales\Eshop\Core\InputValidator;
use OxidEsales\GraphQL\Storefront\Customer\Exception\PasswordValidationException;

final class Password implements PasswordInterface
{
    public function __construct(
        private readonly InputValidator $inputValidator
    ) {
    }

    public function sendPasswordForgotEmail(string $email): bool|int
    {
        $emailService = oxNew(Email::class);
        return $emailService->sendForgotPwdEmail($email);
    }

    /**
     * @throws PasswordValidationException
     */
    public function validatePassword(User $customer, string $newPassword, string $repeatPassword): void
    {
        $exception = $this->inputValidator->checkPassword(
            $customer, $newPassword, $repeatPassword, true);

        if ($exception) {
            throw new PasswordValidationException($exception->getMessage());
        }
    }
}
