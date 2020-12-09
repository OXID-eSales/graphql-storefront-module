<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\NewsletterStatus\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

use function sprintf;

final class NewsletterStatusNotFound extends NotFound
{
    public static function byUserId(string $userId): self
    {
        return new self(sprintf('Newsletter subscription status was not found for userid: %s', $userId));
    }

    public static function byEmail(string $email): self
    {
        return new self(sprintf('Newsletter subscription status was not found for: %s', $email));
    }
}
