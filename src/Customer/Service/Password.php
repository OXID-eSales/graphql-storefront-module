<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Service;

use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Storefront\Customer\Exception\InvalidEmail;
use OxidEsales\GraphQL\Storefront\Customer\Exception\PasswordMismatch;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Password as PasswordInfrastructure;
use OxidEsales\GraphQL\Storefront\Customer\Service\Customer as CustomerService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;

final class Password
{
    public function __construct(
        private readonly Repository $repository,
        private readonly CustomerService $customerService,
        private readonly Authentication $authenticationService,
        private readonly PasswordInfrastructure $passwordInfrastructure,
    ) {
    }

    public function change(string $old, string $new): bool
    {
        $customerModel = $this->customerService
            ->customer(
                (string)$this->authenticationService->getUser()->id()
            )
            ->getEshopModel();

        if (!$customerModel->isSamePassword($old)) {
            throw PasswordMismatch::byOldPassword();
        }

        $customerModel->setPassword($new);

        return $this->repository->saveModel($customerModel);
    }

    public function sendPasswordForgotEmail(string $email): bool
    {
        $isSuccess = $this->passwordInfrastructure->sendPasswordForgotEmail($email);

        if ($isSuccess === false) {
            throw InvalidEmail::byString($email);
        }

        return $isSuccess;
    }

    public function resetPassword(string $updateId, string $newPassword, string $repeatPassword): bool
    {
        if (!$this->passwordInfrastructure->checkPassword($newPassword, $repeatPassword)) {
            return false;
        }

        return $this->passwordInfrastructure->resetPassword($updateId, $newPassword);
    }
}
