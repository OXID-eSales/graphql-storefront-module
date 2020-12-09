<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Contact\Service;

use OxidEsales\GraphQL\Storefront\Contact\DataType\ContactRequest;
use OxidEsales\GraphQL\Storefront\Contact\Infrastructure\Contact as ContactInfrastructure;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class ContactRequestInput
{
    /** @var ContactInfrastructure */
    private $contactInfrastructure;

    public function __construct(
        ContactInfrastructure $contactInfrastructure
    ) {
        $this->contactInfrastructure = $contactInfrastructure;
    }

    /**
     * @Factory
     */
    public function fromUserInput(
        string $email = '',
        string $firstName = '',
        string $lastName = '',
        string $salutation = '',
        string $subject = '',
        string $message = ''
    ): ContactRequest {
        $contactRequest = new ContactRequest(
            $email,
            $firstName,
            $lastName,
            $salutation,
            $subject,
            $message
        );

        $this->contactInfrastructure->assertValidContactRequest($contactRequest);

        return $contactRequest;
    }
}
