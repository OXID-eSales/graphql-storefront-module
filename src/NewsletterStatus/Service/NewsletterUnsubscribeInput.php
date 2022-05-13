<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\NewsletterStatus\Service;

use OxidEsales\GraphQL\Storefront\Customer\Exception\InvalidEmail;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\NewsletterStatusUnsubscribe;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Infrastructure\Repository as NewsletterStatusRepository;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class NewsletterUnsubscribeInput
{
    /** @var NewsletterStatusRepository */
    private $newsletterStatusRepository;

    public function __construct(
        NewsletterStatusRepository $newsletterStatusRepository
    ) {
        $this->newsletterStatusRepository = $newsletterStatusRepository;
    }

    /**
     * @Factory
     */
    public function fromUserInput(string $email): NewsletterStatusUnsubscribe
    {
        $this->assertEmailNotEmpty($email);

        return $this->newsletterStatusRepository->getUnsubscribeByEmail($email);
    }

    private function assertEmailNotEmpty(string $email): bool
    {
        if (!strlen($email)) {
            throw InvalidEmail::byEmptyString();
        }

        return true;
    }
}
