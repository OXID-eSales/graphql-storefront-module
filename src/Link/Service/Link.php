<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Link\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Link\DataType\Link as LinkDataType;
use OxidEsales\GraphQL\Storefront\Link\DataType\LinkFilterList;
use OxidEsales\GraphQL\Storefront\Link\Exception\LinkNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Service\AbstractActiveFilterService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Service\Authorization;
use TheCodingMachine\GraphQLite\Types\ID;

final class Link extends AbstractActiveFilterService
{
    public function __construct(
        Repository $repository,
        Authorization $authorizationService
    ) {
        parent::__construct($repository, $authorizationService);
    }

    /**
     * @throws LinkNotFound
     * @throws InvalidLogin
     */
    public function link(ID $id): LinkDataType
    {
        try {
            /** @var LinkDataType $link */
            $link = $this->repository->getById(
                (string)$id,
                LinkDataType::class
            );
        } catch (NotFound $e) {
            throw LinkNotFound::byId((string)$id);
        }

        if ($link->isActive()) {
            return $link;
        }

        if (!$this->authorizationService->isAllowed('VIEW_INACTIVE_LINK')) {
            throw new InvalidLogin('Unauthorized');
        }

        return $link;
    }

    /**
     * @return LinkDataType[]
     */
    public function links(LinkFilterList $filter): array
    {
        // In case user has VIEW_INACTIVE_LINK permissions
        // return all links including inactive ones
        $this->setActiveFilter($filter);

        return $this->repository->getByFilter(
            $filter,
            LinkDataType::class
        );
    }

    protected function getInactivePermission(): string
    {
        return 'VIEW_INACTIVE_LINK';
    }
}
