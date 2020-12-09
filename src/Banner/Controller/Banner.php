<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Banner\Controller;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Catalogue\Banner\DataType\Banner as BannerDataType;
use OxidEsales\GraphQL\Catalogue\Banner\Exception\BannerNotFound;
use OxidEsales\GraphQL\Catalogue\Banner\Service\Banner as BannerService;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class Banner
{
    /** @var BannerService */
    private $bannerService;

    public function __construct(
        BannerService $bannerService
    ) {
        $this->bannerService = $bannerService;
    }

    /**
     * @Query()
     *
     * @throws BannerNotFound
     * @throws InvalidLogin
     */
    public function banner(string $id): BannerDataType
    {
        return $this->bannerService->banner($id);
    }

    /**
     * @Query()
     *
     * @return BannerDataType[]
     */
    public function banners(): array
    {
        return $this->bannerService->banners();
    }
}
