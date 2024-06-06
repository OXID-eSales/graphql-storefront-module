<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Customer\Exception;

use GraphQL\Error\Error;
use OxidEsales\GraphQL\Base\Exception\ErrorCategories;
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotFoundByUpdateHash;
use OxidEsales\GraphQL\Storefront\Customer\Exception\PasswordMismatch;
use OxidEsales\GraphQL\Storefront\Customer\Exception\PasswordValidationException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotFoundByUpdateHash
 * @covers \OxidEsales\GraphQL\Storefront\Customer\Exception\PasswordValidationException
 * @covers \OxidEsales\GraphQL\Storefront\Customer\Exception\PasswordMismatch
 */
class ExceptionsTest extends TestCase
{
    public function testCustomerNotFoundByUpdateHashException(): void
    {
        $passwordUpdateId = uniqid();
        $exception = new CustomerNotFoundByUpdateHash($passwordUpdateId);
        $this->assertSame(
            'No customer was found by update hash: "' . $passwordUpdateId . '".',
            $exception->getMessage()
        );
    }

    public function testGetCustomerByPasswordUpdateId(): void
    {
        $expectedMessage = uniqid();
        $exception = new PasswordValidationException($expectedMessage);
        $this->assertSame($expectedMessage, $exception->getMessage());
    }

    /** @dataProvider exceptionTypesDataProvider */
    public function testExceptionsHaveCorrectTypes(Error $exception, string $expectedCategory): void
    {
        $this->assertSame($expectedCategory, $exception->getCategory());
    }

    public static function exceptionTypesDataProvider(): \Generator
    {
        yield [
            'exception' => new CustomerNotFoundByUpdateHash(uniqid()),
            'expectedCategory' => ErrorCategories::REQUESTERROR
        ];

        yield [
            'exception' => new PasswordValidationException(uniqid()),
            'expectedCategory' => ErrorCategories::REQUESTERROR
        ];

        yield [
            'exception' => new PasswordMismatch(uniqid()),
            'expectedCategory' => ErrorCategories::REQUESTERROR
        ];
    }
}
