<?php

namespace OxidEsales\GraphQL\Storefront\Customer\Service;

interface PasswordInterface
{
    public function change(string $old, string $new): bool;

    public function sendPasswordForgotEmail(string $email): bool;

    public function resetPasswordByUpdateId(string $updateId, string $newPassword, string $repeatPassword): bool;
}
