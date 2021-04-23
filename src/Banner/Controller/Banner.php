<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Banner\Controller;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Storefront\Banner\DataType\Banner as BannerDataType;
use OxidEsales\GraphQL\Storefront\Banner\Exception\BannerNotFound;
use OxidEsales\GraphQL\Storefront\Banner\Service\Banner as BannerService;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Types\ID;

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
    public function banner(ID $bannerId): BannerDataType
    {
        return $this->bannerService->banner($bannerId);
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
