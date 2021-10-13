<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Action\DataType;

use OxidEsales\GraphQL\Base\DataType\Filter\BoolFilter;
use OxidEsales\GraphQL\Base\DataType\Filter\IntegerFilter;
use OxidEsales\GraphQL\Base\DataType\Filter\StringFilter;
use OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class ActionFilterList extends FilterList
{
    /** @var null|StringFilter */
    protected $actionId;

    public function __construct(
        ?StringFilter $actionId = null,
        ?BoolFilter $active = null
    ) {
        $this->actionId = $actionId;
        $this->active   = $active;
        parent::__construct();
    }

    /**
     * @return array{
     *                oxid: null|StringFilter
     *                }
     */
    public function getFilters(): array
    {
        return [
            'oxid'   => $this->actionId,
            'oxtype' => new IntegerFilter(null, 2),
        ];
    }

    /**
     * @Factory
     */
    public static function createActionFilterList(?StringFilter $actionId = null): self
    {
        return new self(
            $actionId
        );
    }
}
