<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\DeliveryMethod\Service;

use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\DataType\DeliveryMethod as DeliveryMethodDataType;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\Exception\DeliveryMethodNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;

final class DeliveryMethod
{
    /** @var Repository */
    private $repository;

    public function __construct(
        Repository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @throws DeliveryMethodNotFound
     */
    public function getDeliveryMethod(string $id): DeliveryMethodDataType
    {
        try {
            /** @var DeliveryMethodDataType $deliveryMethod */
            $deliveryMethod = $this->repository->getById(
                $id,
                DeliveryMethodDataType::class,
                false
            );
        } catch (NotFound $e) {
            throw DeliveryMethodNotFound::byId($id);
        }

        return $deliveryMethod;
    }
}
