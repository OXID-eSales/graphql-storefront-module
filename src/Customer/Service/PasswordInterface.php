<?php

namespace OxidEsales\GraphQL\Storefront\Customer\Service;

interface PasswordInterface
{
    public function change(string $old, string $new): bool;

    public function sendPasswordForgotEmail(string $email): bool;

    public function resetPasswordByUpdateHash(string $updateHash, string $newPassword, string $repeatPassword): bool;
}
