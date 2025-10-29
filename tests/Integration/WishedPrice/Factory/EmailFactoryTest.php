<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\WishedPrice\Factory;

use OxidEsales\Eshop\Core\Email;
use OxidEsales\GraphQL\Storefront\Tests\Integration\BaseTestCase;
use OxidEsales\GraphQL\Storefront\WishedPrice\Factory\EmailFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(EmailFactory::class)]
final class EmailFactoryTest extends BaseTestCase
{
    #[Test]
    public function createEmail(): void
    {
        $emailFactory = new EmailFactory();
        $email = $emailFactory->create();

        $this->assertInstanceOf(Email::class, $email);
        $this->assertNotSame($email, $emailFactory->create());
    }
}
