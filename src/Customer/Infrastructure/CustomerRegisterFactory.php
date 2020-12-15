<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Infrastructure;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer;

final class CustomerRegisterFactory
{
    public function createCustomer(string $email, string $password, ?DateTimeInterface $birthdate): Customer
    {
        /** @var User $customerModel */
        $customerModel = oxNew(User::class);
        $customerModel->assign([
            'OXUSERNAME' => $email,
        ]);

        if ($birthdate) {
            $customerModel->assign([
                'OXBIRTHDATE' => $birthdate->format('Y-m-d 00:00:00'),
            ]);
        }

        $customerModel->setPassword($password);

        return new Customer($customerModel);
    }
}
