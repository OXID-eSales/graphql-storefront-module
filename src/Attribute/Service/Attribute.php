<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Attribute\Service;

use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Attribute\DataType\Attribute as AttributeDataType;
use OxidEsales\GraphQL\Storefront\Attribute\DataType\AttributeFilterList;
use OxidEsales\GraphQL\Storefront\Attribute\Exception\AttributeNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Service\AbstractActiveFilterService;
use TheCodingMachine\GraphQLite\Types\ID;

final class Attribute extends AbstractActiveFilterService
{
    /**
     * @throws AttributeNotFound
     */
    public function attribute(ID $id): AttributeDataType
    {
        try {
            /** @var AttributeDataType $attribute */
            $attribute = $this->repository->getById(
                (string)$id,
                AttributeDataType::class
            );
        } catch (NotFound $e) {
            throw AttributeNotFound::byId((string)$id);
        }

        return $attribute;
    }

    /**
     * @return AttributeDataType[]
     */
    public function attributes(AttributeFilterList $filter): array
    {
        $this->setActiveFilter($filter);

        return $this->repository->getByFilter(
            $filter,
            AttributeDataType::class
        );
    }

    protected function getInactivePermission(): string
    {
        return 'VIEW_INACTIVE_ATTRIBUTE';
    }
}
