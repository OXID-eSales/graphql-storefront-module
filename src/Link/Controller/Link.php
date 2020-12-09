<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Link\Controller;

use OxidEsales\GraphQL\Catalogue\Link\DataType\Link as LinkDataType;
use OxidEsales\GraphQL\Catalogue\Link\DataType\LinkFilterList;
use OxidEsales\GraphQL\Catalogue\Link\Service\Link as LinkService;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class Link
{
    /** @var LinkService */
    private $linkService;

    public function __construct(
        LinkService $linkService
    ) {
        $this->linkService = $linkService;
    }

    /**
     * @Query()
     */
    public function link(string $id): LinkDataType
    {
        return $this->linkService->link($id);
    }

    /**
     * @Query()
     *
     * @return LinkDataType[]
     */
    public function links(?LinkFilterList $filter = null): array
    {
        return $this->linkService->links(
            $filter ?? new LinkFilterList()
        );
    }
}
