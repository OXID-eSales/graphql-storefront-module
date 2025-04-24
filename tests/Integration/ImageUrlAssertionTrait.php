<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration;

use OxidEsales\Eshop\Core\Config;

trait ImageUrlAssertionTrait
{
    protected function assertUrlIsProductImageUrl(
        string $imageUrl,
        string $fileName,
        string $sizeName,
        int $key = 1
    ): void {
        /** @var Config $config */
        $config = $this->get(Config::class);
        $configValue = match ($sizeName) {
            'image' => $config->getConfigParam('aDetailImageSizes')['oxpic1'],
            'icon' => $config->getConfigParam('sIconsize'),
            'zoom' => $config->getConfigParam('sZoomImageSize'),
            'thumb' => $config->getConfigParam('sThumbnailsize'),
            default => $this->fail('Wrong size name provided')
        };
        $size = str_replace('*', '_', $configValue);

        $this->assertMatchesRegularExpression(
            sprintf('@https?://.*/out/pictures/generated/product/%s/%s_75/%s@', $key, $size, $fileName),
            $imageUrl
        );
    }
}
