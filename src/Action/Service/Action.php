<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Action\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Action\DataType\Action as ActionDataType;
use OxidEsales\GraphQL\Storefront\Action\DataType\ActionFilterList;
use OxidEsales\GraphQL\Storefront\Action\Exception\ActionNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Service\AbstractActiveFilterService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Service\Authorization;
use TheCodingMachine\GraphQLite\Types\ID;

final class Action extends AbstractActiveFilterService
{
    public function __construct(
        Repository $repository,
        Authorization $authorizationService
    ) {
        parent::__construct($repository, $authorizationService);
    }

    /**
     * @throws ActionNotFound
     * @throws InvalidLogin
     */
    public function action(ID $id): ActionDataType
    {
        $id = (string)$id->val();

        try {
            /** @var ActionDataType $action */
            $action = $this->repository->getById(
                $id,
                ActionDataType::class
            );
        } catch (NotFound $e) {
            throw ActionNotFound::byId($id);
        }

        if ($action->isActive()) {
            return $action;
        }

        if ($this->authorizationService->isAllowed('VIEW_INACTIVE_ACTION')) {
            return $action;
        }

        throw new InvalidLogin('Unauthorized');
    }

    /**
     * @return ActionDataType[]
     */
    public function actions(ActionFilterList $filter): array
    {
        // In case user has VIEW_INACTIVE_ACTION permissions
        // return all actions including inactive ones
        $this->setActiveFilter($filter);

        return $this->repository->getByFilter(
            $filter,
            ActionDataType::class
        );
    }

    protected function getInactivePermission(): string
    {
        return 'VIEW_INACTIVE_ACTION';
    }
}
