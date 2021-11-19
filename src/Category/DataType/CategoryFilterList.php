<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Category\DataType;

use OxidEsales\GraphQL\Base\DataType\StringFilter;
use OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList;
use OxidEsales\GraphQL\Storefront\Shared\DataType\SeoSlugFilter;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class CategoryFilterList extends FilterList
{
    /** @var null|StringFilter */
    protected $title;

    //TODO: use IDFilter

    /** @var null|StringFilter */
    protected $parentId;

    /** @var ?SeoSlugFilter */
    private $slug;

    public function __construct(
        ?StringFilter $title = null,
        ?StringFilter $parentId = null,
        ?SeoSlugFilter $slug = null
    ) {
        $this->title    = $title;
        $this->parentId = $parentId;
        $this->slug     = $slug;
        null === $this->slug ?: $this->slug->setType('oxcategory');

        parent::__construct();
    }

    /**
     * @return array{
     *                oxtitle: ?StringFilter,
     *                oxparentid: ?StringFilter,
     *                oxseourl: ?SeoSlugFilter,
     *                }
     */
    public function getFilters(): array
    {
        return [
            'oxtitle'    => $this->title,
            'oxparentid' => $this->parentId,
            'oxseourl'   => $this->slug,
        ];
    }

    /**
     * @Factory(name="CategoryFilterList")
     */
    public static function createCategoryFilterList(
        ?StringFilter $title = null,
        ?StringFilter $parentId = null,
        ?SeoSlugFilter $slug = null
    ): self {
        return new self(
            $title,
            $parentId,
            $slug
        );
    }
}
