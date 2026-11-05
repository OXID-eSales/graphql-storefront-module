<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\GraphQL\Storefront\NewsletterStatus\Infrastructure;

use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\NewsletterStatus as NewsletterStatusType;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\NewsletterStatusUnsubscribe;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Exception\NewsletterStatusForUserNotFound;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Exception\NewsletterStatusNotFound;

interface RepositoryInterface
{
    /**
     * @throws NewsletterStatusForUserNotFound
     */
    public function getByUserId(string $userId): NewsletterStatusType;

    /**
     * @throws NewsletterStatusNotFound
     */
    public function getByEmail(string $email): NewsletterStatusType;

    /**
     * @throws NewsletterStatusNotFound
     */
    public function getUnsubscribeByEmail(string $email): NewsletterStatusUnsubscribe;
}
