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
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerEmailNotFound;
use OxidEsales\GraphQL\Storefront\Customer\Exception\PasswordValidationException;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\OxNewFactoryInterface;

final class Password implements PasswordInterface
{
    public function __construct(
        private readonly OxNewFactoryInterface $oxNewFactory,
        private readonly InputValidator $inputValidator
    ) {
    }

    public function sendPasswordForgotEmail(string $email): bool
    {
        $emailService = $this->oxNewFactory->getModel(Email::class);
        $wasSend = $emailService->sendForgotPwdEmail($email);

        if ($wasSend == false) {
            throw new CustomerEmailNotFound($email);
        } elseif ($wasSend === -1) {
            return false;
        }

        return true;
    }

    /**
     * @throws PasswordValidationException
     */
    public function validatePassword(User $customer, string $newPassword, string $repeatPassword): void
    {
        $exception = $this->inputValidator->checkPassword(
            $customer,
            $newPassword,
            $repeatPassword,
            true
        );

        if ($exception) {
            throw new PasswordValidationException($exception->getMessage());
        }
    }
}
