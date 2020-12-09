<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\NewsletterStatus\Service;

use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Storefront\Customer\Exception\InvalidEmail;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\NewsletterStatusSubscribe as NewsletterStatusSubscribeType;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class NewsletterSubscribeInput
{
    /** @var Authentication */
    private $authenticationService;

    /** @var Legacy */
    private $legacyService;

    public function __construct(
        Authentication $authenticationService,
        Legacy $legacyService
    ) {
        $this->authenticationService = $authenticationService;
        $this->legacyService         = $legacyService;
    }

    /**
     * @Factory
     */
    public function fromUserInput(
        ?string $firstName,
        ?string $lastName,
        ?string $salutation,
        ?string $email
    ): NewsletterStatusSubscribeType {
        $userId = null;

        if (!$email && $this->authenticationService->isLogged()) {
            $email  = $this->authenticationService->getUserName();
            $userId = $this->authenticationService->getUserId();
        } else {
            $this->assertValidEmail((string) $email);
        }

        return new NewsletterStatusSubscribeType(
            (string) $firstName,
            (string) $lastName,
            (string) $salutation,
            (string) $email,
            $userId
        );
    }

    private function assertValidEmail(string $email): bool
    {
        if (!$this->legacyService->isValidEmail($email)) {
            throw InvalidEmail::byString($email);
        }

        return true;
    }
}
