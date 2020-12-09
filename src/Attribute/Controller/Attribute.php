<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Attribute\Controller;

use OxidEsales\GraphQL\Catalogue\Attribute\DataType\Attribute as AttributeDataType;
use OxidEsales\GraphQL\Catalogue\Attribute\DataType\AttributeFilterList;
use OxidEsales\GraphQL\Catalogue\Attribute\Service\Attribute as AttributeService;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class Attribute
{
    /** @var AttributeService */
    private $attributeService;

    public function __construct(
        AttributeService $attributeService
    ) {
        $this->attributeService = $attributeService;
    }

    /**
     * @Query()
     */
    public function attribute(string $id): AttributeDataType
    {
        return $this->attributeService->attribute($id);
    }

    /**
     * @Query()
     *
     * @return AttributeDataType[]
     */
    public function attributes(?AttributeFilterList $filter = null): array
    {
        return $this->attributeService->attributes(
            $filter ?? new AttributeFilterList()
        );
    }
}
