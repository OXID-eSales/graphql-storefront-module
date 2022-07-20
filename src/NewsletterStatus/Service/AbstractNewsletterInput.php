<?php

namespace OxidEsales\GraphQL\Storefront\NewsletterStatus\Service;

use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Storefront\Customer\Exception\InvalidEmail;

abstract class AbstractNewsletterInput
{
    private Legacy $legacyService;

    public function __construct(
        Legacy $legacyService
    ) {
        $this->legacyService = $legacyService;
    }

    protected function assertValidEmail(string $email): bool
    {
        if (!$this->legacyService->isValidEmail($email)) {
            throw InvalidEmail::byString($email);
        }

        return true;
    }
}
