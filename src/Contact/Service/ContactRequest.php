<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Contact\Service;

use OxidEsales\GraphQL\Storefront\Contact\DataType\ContactRequest as ContactRequestDataType;
use OxidEsales\GraphQL\Storefront\Contact\Infrastructure\Contact as ContactInfrastructure;

final class ContactRequest
{
    /** @var ContactInfrastructure */
    private $contactInfrastructure;

    public function __construct(
        ContactInfrastructure $contactInfrastructure
    ) {
        $this->contactInfrastructure = $contactInfrastructure;
    }

    public function sendContactRequest(ContactRequestDataType $contactRequest): bool
    {
        return $this->contactInfrastructure->sendRequest($contactRequest);
    }
}
