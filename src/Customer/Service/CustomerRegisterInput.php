<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Service;

use DateTimeInterface;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer;
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerExists;
use OxidEsales\GraphQL\Storefront\Customer\Exception\InvalidEmail;
use OxidEsales\GraphQL\Storefront\Customer\Exception\PasswordValidationException;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\CustomerRegisterFactory;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\Repository;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class CustomerRegisterInput
{
    /** @var Repository */
    private $repository;

    /** @var Legacy */
    private $legacyService;

    /** @var CustomerRegisterFactory */
    private $customerRegisterFactory;

    public function __construct(
        Repository $repository,
        Legacy $legacyService,
        CustomerRegisterFactory $customerRegisterFactory
    ) {
        $this->repository = $repository;
        $this->legacyService = $legacyService;
        $this->customerRegisterFactory = $customerRegisterFactory;
    }

    /**
     * @Factory
     */
    public function fromUserInput(string $email, string $password, ?DateTimeInterface $birthdate): Customer
    {
        if (!strlen($email)) {
            throw InvalidEmail::byEmptyString();
        }

        if (!$this->legacyService->isValidEmail($email)) {
            throw InvalidEmail::byString($email);
        }

        if (
            strlen($password) == 0 ||
            (strlen($password) < $this->legacyService->getConfigParam('iPasswordLength'))
        ) {
            throw new PasswordValidationException('Password does not match length requirements');
        }

        if ($this->repository->checkEmailExists($email)) {
            throw CustomerExists::byEmail($email);
        }

        return $this->customerRegisterFactory->createCustomer($email, $password, $birthdate);
    }
}
