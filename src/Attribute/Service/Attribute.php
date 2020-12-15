<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Attribute\Service;

use OxidEsales\GraphQL\Base\DataType\BoolFilter;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Storefront\Attribute\DataType\Attribute as AttributeDataType;
use OxidEsales\GraphQL\Storefront\Attribute\DataType\AttributeFilterList;
use OxidEsales\GraphQL\Storefront\Attribute\Exception\AttributeNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;

final class Attribute
{
    /** @var Repository */
    private $repository;

    /** @var Authorization */
    private $authorizationService;

    public function __construct(
        Repository $repository,
        Authorization $authorizationService
    ) {
        $this->repository           = $repository;
        $this->authorizationService = $authorizationService;
    }

    /**
     * @throws AttributeNotFound
     */
    public function attribute(string $id): AttributeDataType
    {
        try {
            /** @var AttributeDataType $attribute */
            $attribute = $this->repository->getById(
                $id,
                AttributeDataType::class
            );
        } catch (NotFound $e) {
            throw AttributeNotFound::byId($id);
        }

        return $attribute;
    }

    /**
     * @return AttributeDataType[]
     */
    public function attributes(AttributeFilterList $filter): array
    {
        if (!$this->authorizationService->isAllowed('VIEW_INACTIVE_ATTRIBUTE')) {
            $filter = $filter->withActiveFilter(new BoolFilter(true));
        }

        return $this->repository->getByFilter(
            $filter,
            AttributeDataType::class
        );
    }
}
