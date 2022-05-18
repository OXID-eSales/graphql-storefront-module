<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Integration\Controller;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer;
use OxidEsales\GraphQL\Storefront\Customer\Service\Customer as CustomerService;
use OxidEsales\GraphQL\Storefront\Tests\Integration\BaseTestCase;

final class CustomerTest extends BaseTestCase
{
    private const USER_ID = '_testcustomer';

    protected function tearDown(): void
    {
        $this->cleanUpTable('oxuser');

        parent::tearDown();
    }

    public function testRegistrationMailOnCreateUser(): void
    {
        $user = $this->createPartialMock(User::class, ['sendRegistrationEmail', 'getCoreTableName']);
        $user->expects($this->once())->method('sendRegistrationEmail');
        $user->expects($this->any())->method('getCoreTableName')->willReturn('oxuser');
        $user->setId(self::USER_ID);
        $user->assign([
            'oxuser__oxemail' => 'testuser@oxid.de'
        ]);

        $container = ContainerFactory::getInstance()->getContainer();
        $customerService = $container->get(CustomerService::class);

        $customer = new Customer($user);
        $customerService->create($customer);
    }
}
