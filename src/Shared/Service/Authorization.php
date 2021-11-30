<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Service;

use OxidEsales\GraphQL\Base\Service\Authorization as BaseAuthorization;
use OxidEsales\GraphQL\Storefront\Shared\Exception\GraphQLServiceNotFound;
use TheCodingMachine\GraphQLite\Security\AuthorizationServiceInterface;

final class Authorization implements AuthorizationServiceInterface
{
    /** @var ?BaseAuthorization */
    private $authorization;

    public function __construct(?BaseAuthorization $authorization = null)
    {
        $this->authorization = $authorization;
    }

    public function isAllowed(string $right, $subject = null): bool
    {
        if (null === $this->authorization) {
            throw GraphQLServiceNotFound::byServiceName(BaseAuthorization::class);
        }

        return $this->authorization->isAllowed($right, $subject);
    }
}
