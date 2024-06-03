<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Customer\Exception;

use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotFoundByUpdateId;
use OxidEsales\GraphQL\Storefront\Customer\Exception\PasswordValidationException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotFoundByUpdateId
 * @covers \OxidEsales\GraphQL\Storefront\Customer\Exception\PasswordValidationException
 */
class ExceptionsTest extends TestCase
{

    public function testCustomerNotFoundByUpdateIdException(): void
    {
        $exception = new CustomerNotFoundByUpdateId('1234');
        $this->assertSame('No customer was found by update id: "1234".', $exception->getMessage());
    }

    public function testGetCustomerByPasswordUpdateId(): void
    {
        $exception = new PasswordValidationException('Password is not long enough.');
        $this->assertSame('Password is not long enough.', $exception->getMessage());

        $exception = new PasswordValidationException('Password does not match with th repeated password.');
        $this->assertSame('Password does not match with th repeated password.', $exception->getMessage());
    }
}
