<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Contact\Service;

use OxidEsales\GraphQL\Storefront\Contact\Infrastructure\Contact as ContactInfrastructure;

abstract class ContactInfrastructureAwareService
{
    /** @var ContactInfrastructure */
    protected $contactInfrastructure;

    public function __construct(
        ContactInfrastructure $contactInfrastructure
    ) {
        $this->contactInfrastructure = $contactInfrastructure;
    }
}
