<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\NewsletterStatus\Service;

use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Storefront\Customer\Exception\InvalidEmail;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\NewsletterStatus as NewsletterStatusType;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\Subscriber as SubscriberType;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Exception\NewsletterStatusNotFound;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Exception\SubscriberNotFound;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Infrastructure\RepositoryInterface as NewsletterStatusRepository;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class NewsletterOptInInput extends AbstractNewsletterInput
{
    /** @var SubscriberInterface */
    private $subscriberService;

    /** @var NewsletterStatusRepository */
    private $newsletterStatusRepository;

    public function __construct(
        SubscriberInterface $subscriberService,
        NewsletterStatusRepository $newsletterStatusRepository,
        Legacy $legacyService
    ) {
        $this->subscriberService = $subscriberService;
        $this->newsletterStatusRepository = $newsletterStatusRepository;

        parent::__construct($legacyService);
    }

    #[Factory]
    public function fromUserInput(string $email, string $confirmCode): NewsletterStatusType
    {
        $this->assertValidEmail($email);

        try {
            $newsletterStatus = $this->newsletterStatusRepository->getByEmail($email);
            /** @var SubscriberType $subscriber */
            $subscriber = $this->subscriberService->subscriber((string)$newsletterStatus->userId());
        } catch (NewsletterStatusNotFound | SubscriberNotFound $exception) {
            throw InvalidEmail::byConfirmationCode($confirmCode);
        }

        $this->verifyConfirmCode($subscriber, $confirmCode);

        return $newsletterStatus;
    }

    /**
     * @throws InvalidEmail
     */
    private function verifyConfirmCode(SubscriberType $subcriber, string $confirmCode): void
    {
        if ($subcriber->getConfirmationCode() !== $confirmCode) {
            throw InvalidEmail::byConfirmationCode($confirmCode);
        }
    }
}
