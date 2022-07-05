<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\NewsletterStatus\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

use function sprintf;

final class NewsletterStatusForUserNotFound extends NotFound
{
    public function __construct(string $userId)
    {
        parent::__construct(sprintf('Newsletter subscription status was not found for userid: %s', $userId));
    }
}
