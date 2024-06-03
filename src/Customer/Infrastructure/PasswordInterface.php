<?php

namespace OxidEsales\GraphQL\Storefront\Customer\Infrastructure;

use OxidEsales\Eshop\Application\Model\User;

interface PasswordInterface
{
    public function sendPasswordForgotEmail(string $email): bool|int;

    public function validatePassword(User $customer, string $newPassword, string $repeatPassword): void;
}
