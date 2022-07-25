<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Content\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Content\DataType\Content as ContentDataType;
use OxidEsales\GraphQL\Storefront\Content\DataType\ContentFilterList;
use OxidEsales\GraphQL\Storefront\Content\Exception\ContentNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Service\AbstractActiveFilterService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Service\Authorization;
use TheCodingMachine\GraphQLite\Types\ID;

final class Content extends AbstractActiveFilterService
{
    /**
     * @throws ContentNotFound
     * @throws InvalidLogin
     */
    public function content(ID $id): ContentDataType
    {
        try {
            $content = $this->repository->getById(
                (string)$id,
                ContentDataType::class,
                false
            );
        } catch (NotFound $e) {
            throw ContentNotFound::byId((string)$id);
        }

        if ($content->isActive()) {
            return $content;
        }

        if (!$this->authorizationService->isAllowed('VIEW_INACTIVE_CONTENT')) {
            throw new InvalidLogin('Unauthorized');
        }

        return $content;
    }

    /**
     * @return ContentDataType[]
     */
    public function contents(ContentFilterList $filter): array
    {
        // In case user has VIEW_INACTIVE_CONTENT permissions
        // return all contents including inactive
        $this->setActiveFilter($filter);

        return $this->repository->getByFilter(
            $filter,
            ContentDataType::class
        );
    }

    protected function getInactivePermission(): string
    {
        return 'VIEW_INACTIVE_CONTENT';
    }
}
