<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Infrastructure;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Email;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\GraphQL\Storefront\Customer\Exception\PasswordMismatch;

final class Password
{
    public function sendPasswordForgotEmail(string $email): bool|int
    {
        $emailService = oxNew(Email::class);
        return $emailService->sendForgotPwdEmail($email);
    }

    public function checkPassword(string $newPassword, string $repeatPassword): bool
    {
        try {
            $this->validatePassword($newPassword, $repeatPassword);
        } catch (StandardException $exception) {
            $graphqlException = $this->convertException($exception);
            if ($graphqlException) {
                throw $graphqlException;
            }

            return false;
        }

        return true;
    }

    private function validatePassword(string $newPassword, string $repeatPassword): void
    {
        $user = oxNew(User::class);
        $inputValidator = Registry::getInputValidator();
        $exception = $inputValidator->checkPassword($user, $newPassword, $repeatPassword, true);

        if ($exception) {
            throw $exception;
        }
    }

    private function convertException(StandardException $exception): ?PasswordMismatch
    {
        if (
            $exception->getMessage() == $this->translate('ERROR_MESSAGE_INPUT_EMPTYPASS')
            || $exception->getMessage() == $this->translate('ERROR_MESSAGE_PASSWORD_TOO_SHORT')
        ) {
            return PasswordMismatch::byLength();
        }

        if ($exception->getMessage() == $this->translate('ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH')) {
            return PasswordMismatch::byMismatch();
        }

        return null;
    }

    private function translate(string $string): string
    {
        return Registry::getLang()->translateString($string);
    }

    public function resetPassword(string $updateId, string $newPassword): bool
    {
        $user = oxNew(User::class);
        $user->loadUserByUpdateId($updateId);
        if (!$user->isLoaded()) {
            return false;
        }

        $user->setPassword($newPassword);
        $user->setUpdateKey(true);
        $user->save();

        return true;
    }
}
