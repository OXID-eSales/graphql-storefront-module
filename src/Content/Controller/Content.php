<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Content\Controller;

use OxidEsales\GraphQL\Catalogue\Content\DataType\Content as ContentDataType;
use OxidEsales\GraphQL\Catalogue\Content\DataType\ContentFilterList;
use OxidEsales\GraphQL\Catalogue\Content\Service\Content as ContentService;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class Content
{
    /** @var ContentService */
    private $contentService;

    public function __construct(
        ContentService $contentService
    ) {
        $this->contentService = $contentService;
    }

    /**
     * @Query()
     */
    public function content(string $id): ContentDataType
    {
        return $this->contentService->content($id);
    }

    /**
     * @Query()
     *
     * @return ContentDataType[]
     */
    public function contents(?ContentFilterList $filter = null): array
    {
        return $this->contentService->contents(
            $filter ?? new ContentFilterList()
        );
    }
}
