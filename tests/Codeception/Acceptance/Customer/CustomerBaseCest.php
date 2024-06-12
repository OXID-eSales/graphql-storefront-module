<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Codeception\Acceptance\Customer;

use OxidEsales\GraphQL\Storefront\Tests\Codeception\AcceptanceTester;

abstract class CustomerBaseCest
{
    private const AGENT_USERNAME = 'tobedeleted@oxid-esales.com';

    private const AGENT_PASSWORD = 'useruser';

    private const ADMIN_USERNAME = 'admin';

    private const ADMIN_PASSWORD = 'admin';

    public function _after(AcceptanceTester $I): void
    {
        $I->logout();
    }

    protected function getAgentUsername(): string
    {
        return self::AGENT_USERNAME;
    }

    protected function getAgentPassword(): string
    {
        return self::AGENT_PASSWORD;
    }

    protected function getAdminUsername(): string
    {
        return self::ADMIN_USERNAME;
    }

    protected function getAdminPassword(): string
    {
        return self::ADMIN_PASSWORD;
    }
}
