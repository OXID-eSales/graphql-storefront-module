<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Controller;

use OxidEsales\GraphQL\Storefront\Customer\Service\PasswordInterface;
use TheCodingMachine\GraphQLite\Annotations\HideIfUnauthorized;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class Password
{
    /** @var PasswordInterface */
    private $passwordService;

    public function __construct(
        PasswordInterface $passwordService
    ) {
        $this->passwordService = $passwordService;
    }

    /**
     * @Mutation()
     * @Logged()
     * @HideIfUnauthorized()
     */
    public function customerPasswordChange(string $old, string $new): bool
    {
        return $this->passwordService->change($old, $new);
    }

    #[Mutation]
    public function customerPasswordForgotRequest(string $email): bool
    {
        return $this->passwordService->sendPasswordForgotEmail($email);
    }

    #[Mutation]
    public function customerPasswordReset(string $updateHash, string $newPassword, string $repeatPassword): bool
    {
        return $this->passwordService->resetPasswordByUpdateHash($updateHash, $newPassword, $repeatPassword);
    }
}
