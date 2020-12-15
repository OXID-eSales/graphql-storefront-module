<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Contact\Controller;

use OxidEsales\GraphQL\Storefront\Contact\DataType\ContactRequest;
use OxidEsales\GraphQL\Storefront\Contact\Service\ContactRequest as ContactRequestService;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class Contact
{
    /**
     * @var ContactRequestService
     */
    private $contactRequestService;

    public function __construct(
        ContactRequestService $contactRequestService
    ) {
        $this->contactRequestService = $contactRequestService;
    }

    /**
     * @Mutation()
     */
    public function contactRequest(ContactRequest $request): bool
    {
        return $this->contactRequestService->sendContactRequest($request);
    }
}
