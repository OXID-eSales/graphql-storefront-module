<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Payment\Service;

use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Payment\DataType\Payment as PaymentDataType;
use OxidEsales\GraphQL\Storefront\Payment\Exception\PaymentNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;

final class Payment
{
    /** @var Repository */
    private $repository;

    public function __construct(
        Repository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @throws PaymentNotFound
     */
    public function payment(string $id): ?PaymentDataType
    {
        try {
            /** @var PaymentDataType $payment */
            $payment = $this->repository->getById($id, PaymentDataType::class);
        } catch (NotFound $e) {
            throw new PaymentNotFound($id);
        }

        return $payment->isActive() ? $payment : null;
    }
}
