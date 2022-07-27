<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\NewsletterStatus\Service;

use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\NewsletterStatusUnsubscribe;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Infrastructure\Repository as NewsletterStatusRepository;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class NewsletterUnsubscribeInput extends AbstractNewsletterInput
{
    /** @var NewsletterStatusRepository */
    private $newsletterStatusRepository;

    public function __construct(
        NewsletterStatusRepository $newsletterStatusRepository,
        Legacy $legacyService
    ) {
        $this->newsletterStatusRepository = $newsletterStatusRepository;

        parent::__construct($legacyService);
    }

    /**
     * @Factory
     */
    public function fromUserInput(string $email): NewsletterStatusUnsubscribe
    {
        $this->assertValidEmail($email);

        return $this->newsletterStatusRepository->getUnsubscribeByEmail($email);
    }
}
