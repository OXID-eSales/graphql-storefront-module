<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Link\Controller;

use OxidEsales\GraphQL\Storefront\Link\DataType\Link as LinkDataType;
use OxidEsales\GraphQL\Storefront\Link\DataType\LinkFilterList;
use OxidEsales\GraphQL\Storefront\Link\Service\Link as LinkService;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Types\ID;

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
    public function link(ID $linkId): LinkDataType
    {
        return $this->linkService->link($linkId);
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
