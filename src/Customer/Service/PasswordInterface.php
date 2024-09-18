<?php

namespace OxidEsales\GraphQL\Storefront\Customer\Service;

use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;

interface PasswordInterface
{
    public function change(string $old, string $new): CustomerDataType;

    public function sendPasswordForgotEmail(string $email): bool;

    public function resetPasswordByUpdateHash(string $updateHash, string $newPassword, string $repeatPassword): bool;
}
