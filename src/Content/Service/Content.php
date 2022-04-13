<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Content\Service;

use OxidEsales\GraphQL\Base\DataType\PaginationFilter;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Content\DataType\Content as ContentDataType;
use OxidEsales\GraphQL\Storefront\Content\DataType\ContentFilterList;
use OxidEsales\GraphQL\Storefront\Content\DataType\Sorting as ContentSorting;
use OxidEsales\GraphQL\Storefront\Content\Exception\ContentNotFound;
use OxidEsales\GraphQL\Storefront\Shared\DataType\SeoSlugFilter;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use OxidEsales\GraphQL\Storefront\Shared\Service\Authorization;
use TheCodingMachine\GraphQLite\Types\ID;

final class Content
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
     * @throws ContentNotFound
     * @throws InvalidLogin
     */
    public function content(?ID $id, ?string $slug): ContentDataType
    {
        if ((!$id && !$slug) || ($id && $slug)) {
            throw ContentNotFound::byParameter();
        }

        try {
            if ($id) {
                /** @var ContentDataType $content */
                $content = $this->repository->getById((string) $id, ContentDataType::class, false);
            } else {
                $content = $this->getContentBySeoSlug((string) $slug);
            }
        } catch (ContentNotFound $e) {
            throw $e;
        } catch (NotFound $e) {
            throw ContentNotFound::byId((string) $id);
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
        if ($this->authorizationService->isAllowed('VIEW_INACTIVE_CONTENT')) {
            $filter = $filter->withActiveFilter(null);
        }

        return $this->repository->getByFilter(
            $filter,
            ContentDataType::class
        );
    }

    /**
     * @throws ContentNotFound
     */
    private function getContentBySeoSlug(string $slug): ContentDataType
    {
        $slugFilter = SeoSlugFilter::fromUserInput(trim($slug, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
        $slugFilter->setType('oxcontent');
        $slugFilter->unsetPostfix();

        $results = $this->repository->getList(
            ContentDataType::class,
            new ContentFilterList(
                null,
                $slugFilter
            ),
            new PaginationFilter(),
            new ContentSorting()
        );

        if (empty($results)) {
            throw ContentNotFound::bySlug($slug);
        }

        if (1 < count($results)) {
            throw ContentNotFound::byAmbiguousBySlug($slug);
        }

        return $results[0];
    }
}
