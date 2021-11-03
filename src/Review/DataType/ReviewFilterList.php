<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Review\DataType;

use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class ReviewFilterList extends FilterList
{
    /** @var ?IDFilter */
    private $user;

    public function __construct(
        ?IDFilter $user = null
    ) {
        $this->user = $user;
        parent::__construct();
    }

    /**
     * @return array{
     *                oxuserid: ?IDFilter,
     *                }
     */
    public function getFilters(): array
    {
        return [
            'oxuserid' => $this->user,
        ];
    }

    /**
     * @Factory
     */
    public static function createProductFilterList(
        ?IDFilter $user = null
    ): self {
        return new self($user);
    }
}
