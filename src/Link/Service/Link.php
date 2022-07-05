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
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Service\Authorization;
use TheCodingMachine\GraphQLite\Types\ID;

final class Link
{
    /** @var Repository */
    private $repository;

    /** @var Authorization */
    private $authorizationService;

    public function __construct(
        Repository $repository,
        Authorization $authorizationService
    ) {
        $this->repository = $repository;
        $this->authorizationService = $authorizationService;
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
            throw new LinkNotFound((string)$id);
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
        if ($this->authorizationService->isAllowed('VIEW_INACTIVE_LINK')) {
            $filter = $filter->withActiveFilter(null);
        }

        return $this->repository->getByFilter(
            $filter,
            LinkDataType::class
        );
    }
}
