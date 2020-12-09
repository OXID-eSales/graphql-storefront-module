<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Banner\Infrastructure;

use OxidEsales\Eshop\Application\Model\ActionList;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\GraphQL\Catalogue\Banner\DataType\Banner as BannerDataType;

final class Banner
{
    /**
     * @return BannerDataType[]
     */
    public function banners(?string $userId): array
    {
        /** @var ActionList $actionList */
        $actionList = oxNew(ActionList::class);
        $actionList->loadBanners();

        if ($userId) {
            /** @var User $user */
            $user = oxNew(User::class);
            $user->load($userId);

            if ($user->isLoaded()) {
                $actionList->setUser($user);
            }
        }

        $result = [];

        if ($banners = $actionList->getArray()) {
            foreach ($banners as $oneBannerModelItem) {
                $result[] = new BannerDataType($oneBannerModelItem);
            }
        }

        return $result;
    }

    /**
     * @return ?string
     */
    public function getProductId(BannerDataType $banner): ?string
    {
        /*
         * NOTE: getBannerArticle will load product but we need to make sure
         * customer have correct permission to see that product
         * by loading product thru product service
         * which can lead to performance issue due to double load of that product
         */
        /** @var null|\OxidEsales\Eshop\Application\Model\Article $product */
        $product = $banner->getEshopModel()->getBannerArticle();

        return $product === null ? $product : $product->getId();
    }
}
