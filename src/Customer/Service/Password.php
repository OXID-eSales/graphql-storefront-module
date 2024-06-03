<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Service;

use OxidEsales\GraphQL\Storefront\Customer\Exception\InvalidEmail;
use OxidEsales\GraphQL\Storefront\Customer\Exception\PasswordMismatch;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\PasswordInterface as PasswordInfrastructuredInterface;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\RepositoryInterface;
use TheCodingMachine\GraphQLite\Security\AuthenticationServiceInterface;

final class Password implements PasswordInterface
{
    public function __construct(
        private readonly RepositoryInterface $repository,
        private readonly CustomerInterface $customerService,
        private readonly AuthenticationServiceInterface $authenticationService,
        private readonly PasswordInfrastructuredInterface $passwordInfrastructure,
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

        return $this->repository->saveNewPasswordForCustomer($customerModel, $new);
    }

    public function sendPasswordForgotEmail(string $email): bool
    {
        $isSuccess = $this->passwordInfrastructure->sendPasswordForgotEmail($email);

        if ($isSuccess === false) {
            throw InvalidEmail::byString($email);
        }

        return $isSuccess;
    }

    public function resetPasswordByUpdateId(string $updateId, string $newPassword, string $repeatPassword): bool
    {
        $customer = $this->repository->getCustomerByPasswordUpdateId($updateId);
        $this->passwordInfrastructure->validatePassword($customer, $newPassword, $repeatPassword);
        return $this->repository->saveNewPasswordForCustomer($customer, $newPassword);
    }
}
