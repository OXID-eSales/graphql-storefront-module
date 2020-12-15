<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Action\Controller;

use OxidEsales\GraphQL\Storefront\Action\DataType\Action as ActionDataType;
use OxidEsales\GraphQL\Storefront\Action\DataType\ActionFilterList;
use OxidEsales\GraphQL\Storefront\Action\Service\Action as ActionService;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class Action
{
    /** @var ActionService */
    private $actionService;

    public function __construct(
        ActionService $actionService
    ) {
        $this->actionService = $actionService;
    }

    /**
     * @Query()
     */
    public function action(string $id): ActionDataType
    {
        return $this->actionService->action($id);
    }

    /**
     * @Query()
     *
     * @return ActionDataType[]
     */
    public function actions(?ActionFilterList $filter = null): array
    {
        return $this->actionService->actions(
            $filter ?? new ActionFilterList()
        );
    }
}
