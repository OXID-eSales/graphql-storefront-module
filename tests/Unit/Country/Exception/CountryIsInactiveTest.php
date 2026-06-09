<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Country\Exception;

use OxidEsales\GraphQL\Storefront\Country\Exception\CountryIsInactive;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CountryIsInactive::class)]
final class CountryIsInactiveTest extends TestCase
{
    #[Test]
    public function exceptionMessageContainsCountryId(): void
    {
        $countryId = uniqid();

        $this->expectException(CountryIsInactive::class);
        $this->expectExceptionMessage('Country is inactive: ' . $countryId);

        throw new CountryIsInactive($countryId);
    }
}
