<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Controller;

use OxidEsales\GraphQL\Base\DataType\Login as LoginDatatype;
use OxidEsales\GraphQL\Base\DataType\LoginInterface;
use OxidEsales\GraphQL\Base\Service\LoginServiceInterface;
use OxidEsales\GraphQL\Storefront\Customer\Service\PasswordInterface;
use TheCodingMachine\GraphQLite\Annotations\HideIfUnauthorized;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class Password
{
    public function __construct(
        protected PasswordInterface $passwordService,
        private readonly LoginServiceInterface $loginService,
    ) {
    }

    /**
     * @Mutation()
     * @Logged()
     * @HideIfUnauthorized()
     */
    public function customerPasswordChange(string $old, string $new): LoginInterface
    {
        $customer = $this->passwordService->change($old, $new);

        return $this->loginService->login($customer->getEmail(), $new);
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
