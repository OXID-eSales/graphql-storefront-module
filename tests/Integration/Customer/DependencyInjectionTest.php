<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Customer;

use OxidEsales\GraphQL\Storefront\Customer\Controller\Password;
use OxidEsales\GraphQL\Storefront\Tests\Integration\BaseTestCase;
use PHPUnit\Framework\Attributes\Test;

final class DependencyInjectionTest extends BaseTestCase
{
    #[Test]
    public function testPasswordControllerDIChain(): void
    {
        $this->get(Password::class);
    }
}
