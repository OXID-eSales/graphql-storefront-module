<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\NewsletterStatus\Exception;

use OxidEsales\GraphQL\Base\Exception\NotFound;

use function sprintf;

final class SubscriberNotFound extends NotFound
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Subscriber was not found by id: %s', $id));
    }
}
