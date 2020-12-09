<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Banner\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\InvalidToken;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Catalogue\Banner\DataType\Banner as BannerDataType;
use OxidEsales\GraphQL\Catalogue\Banner\Exception\BannerNotFound;
use OxidEsales\GraphQL\Catalogue\Banner\Infrastructure\Banner as BannerInfrastructure;
use OxidEsales\GraphQL\Catalogue\Shared\Infrastructure\Repository;

final class Banner
{
    /** @var Repository */
    private $repository;

    /** @var Authorization */
    private $authorizationService;

    /** @var Authentication */
    private $authenticationService;

    /** @var BannerInfrastructure */
    private $bannerInfrastructure;

    public function __construct(
        Repository $repository,
        Authorization $authorizationService,
        Authentication $authenticationService,
        BannerInfrastructure $bannerInfrastructure
    ) {
        $this->repository            = $repository;
        $this->authorizationService  = $authorizationService;
        $this->bannerInfrastructure  = $bannerInfrastructure;
        $this->authenticationService = $authenticationService;
    }

    /**
     * @throws BannerNotFound
     * @throws InvalidLogin
     */
    public function banner(string $id): BannerDataType
    {
        try {
            /** @var BannerDataType $banner */
            $banner = $this->repository->getById($id, BannerDataType::class);
        } catch (NotFound $e) {
            throw BannerNotFound::byId($id);
        }

        if ($banner->isActive()) {
            return $banner;
        }

        if (!$this->authorizationService->isAllowed('VIEW_INACTIVE_BANNER')) {
            throw new InvalidLogin('Unauthorized');
        }

        return $banner;
    }

    /**
     * @return BannerDataType[]
     */
    public function banners(): array
    {
        $userId = null;

        try {
            $userId = $this->authenticationService->getUserId();
        } catch (InvalidToken $e) {
        }

        return $this->bannerInfrastructure->banners($userId);
    }
}
