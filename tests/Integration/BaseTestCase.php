<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration;

use OxidEsales\GraphQL\Base\Tests\Integration\TokenTestCase;

abstract class BaseTestCase extends TokenTestCase
{
    protected function doAssertArraySubset($needle, $haystack): void
    {
        if (method_exists($this, 'assertArraySubsetOxid')) {
            parent::assertArraySubsetOxid($needle, $haystack);
        } else {
            parent::assertArraySubset($needle, $haystack);
        }
    }

    protected function doAssertContains($needle, $haystack, $message = ''): void
    {
        if (method_exists($this, 'assertStringContainsString')) {
            parent::assertStringContainsString($needle, $haystack, $message);
        } else {
            parent::assertContains($needle, $haystack, $message);
        }
    }
}
