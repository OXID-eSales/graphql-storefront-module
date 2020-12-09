<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Action\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Catalogue\Action\DataType\Action as ActionDataType;
use OxidEsales\GraphQL\Catalogue\Action\DataType\ActionFilterList;
use OxidEsales\GraphQL\Catalogue\Action\Exception\ActionNotFound;
use OxidEsales\GraphQL\Catalogue\Shared\Infrastructure\Repository;

final class Action
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
     * @throws ActionNotFound
     * @throws InvalidLogin
     */
    public function action(string $id): ActionDataType
    {
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
        if ($this->authorizationService->isAllowed('VIEW_INACTIVE_ACTION')) {
            $filter = $filter->withActiveFilter(null);
        }

        return $this->repository->getByFilter(
            $filter,
            ActionDataType::class
        );
    }
}
