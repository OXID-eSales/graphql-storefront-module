<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\NewsletterStatus\Service;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Service\Authentication;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\NewsletterStatus as NewsletterStatusType;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\NewsletterStatusSubscribe;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\NewsletterStatusUnsubscribe;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\Subscriber as SubscriberType;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Exception\SubscriberNotFound;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Infrastructure\NewsletterStatus as NewsletterStatusRepository;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Infrastructure\Repository as NewsletterSubscriptionRepository;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Service\Subscriber as SubscriberService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;

final class NewsletterStatus
{
    /** @var NewsletterSubscriptionRepository */
    private $NewsletterSubscriptionRepository;

    /** @var NewsletterStatusRepository */
    private $NewsletterStatusRepository;

    /** @var Repository */
    private $repository;

    /** @var SubscriberService */
    private $subscriberService;

    /** @var Authentication */
    private $authenticationService;

    public function __construct(
        NewsletterSubscriptionRepository $NewsletterSubscriptionRepository,
        NewsletterStatusRepository $NewsletterStatusRepository,
        Authentication $authenticationService,
        Repository $repository,
        SubscriberService $subscriberService
    ) {
        $this->NewsletterSubscriptionRepository = $NewsletterSubscriptionRepository;
        $this->NewsletterStatusRepository = $NewsletterStatusRepository;
        $this->authenticationService = $authenticationService;
        $this->repository = $repository;
        $this->subscriberService = $subscriberService;
    }

    public function newsletterStatus(): NewsletterStatusType
    {
        /** Only logged in users can query their newsletter status */
        if (!$this->authenticationService->isLogged()) {
            throw new InvalidLogin('Unauthenticated');
        }

        return $this->NewsletterSubscriptionRepository->getByUserId(
            (string)$this->authenticationService->getUser()->id()
        );
    }

    public function optIn(NewsletterStatusType $newsletterStatus): NewsletterStatusType
    {
        $subscriber = $this->subscriberService->subscriber((string)$newsletterStatus->userId());

        $this->NewsletterStatusRepository->optIn($subscriber, $newsletterStatus);

        return $this->NewsletterSubscriptionRepository->getByUserId(
            $subscriber->getId()
        );
    }

    public function unsubscribe(?NewsletterStatusUnsubscribe $newsletterStatus): bool
    {
        $userId = null;

        if ($newsletterStatus) {
            $userId = (string)$newsletterStatus->userId();
        } elseif ($this->authenticationService->isLogged()) {
            $userId = (string)$this->authenticationService->getUser()->id();
        }

        /** If we don't have email from token or as parameter */
        if (!$userId) {
            throw new SubscriberNotFound('Missing subscriber email or token');
        }

        $subscriber = $this->subscriberService->subscriber($userId);

        return $this->NewsletterStatusRepository->unsubscribe($subscriber);
    }

    public function subscribe(NewsletterStatusSubscribe $newsletterStatusSubscribe): NewsletterStatusType
    {
        $customer = $this->NewsletterStatusRepository->createNewsletterUser($newsletterStatusSubscribe);
        $subscriber = new SubscriberType($customer->getEshopModel());

        return $this->NewsletterStatusRepository->subscribe(
            $subscriber,
            $newsletterStatusSubscribe->userId() ? false : true
        );
    }
}
