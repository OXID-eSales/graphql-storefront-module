<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\NewsletterStatus\Infrastructure;

use OxidEsales\Eshop\Application\Model\NewsSubscribed as EshopNewsletterSubscriptionStatusModel;
use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyService;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;
use OxidEsales\GraphQL\Storefront\Customer\Exception\CustomerNotFound;
use OxidEsales\GraphQL\Storefront\Customer\Infrastructure\RepositoryInterface as CustomerRepository;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\NewsletterStatus as NewsletterStatusType;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\NewsletterStatusSubscribe as NewsletterStatusSubscribeType;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\Subscriber as SubscriberDataType;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Infrastructure\Repository as NewsletterStatusRepository;

final class NewsletterStatus
{
    /** @var CustomerRepository */
    private $customerRepository;

    /** @var LegacyService */
    private $legacyService;

    /** @var NewsletterStatusRepository */
    private $newsletterStatusRepository;

    public function __construct(
        CustomerRepository $customerRepository,
        LegacyService $legacyService,
        NewsletterStatusRepository $newsletterStatusRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->legacyService = $legacyService;
        $this->newsletterStatusRepository = $newsletterStatusRepository;
    }

    public function optIn(SubscriberDataType $subscriber, NewsletterStatusType $newsletterStatus): bool
    {
        /** @var EshopNewsletterSubscriptionStatusModel $newsletterStatusModel */
        $newsletterStatusModel = $newsletterStatus->getEshopModel();
        $newsletterStatusModel->setOptInStatus(1);

        return $newsletterStatusModel->updateSubscription($subscriber->getEshopModel());
    }

    public function unsubscribe(SubscriberDataType $subscriber): bool
    {
        return $this->setNewsSubscription($subscriber, false);
    }

    public function subscribe(
        SubscriberDataType $subscriber,
        bool $forceOptin
    ): NewsletterStatusType {
        if ($forceOptin) {
            $this->unsubscribe($subscriber);
        }
        $this->setNewsSubscription($subscriber, true);

        return $this->newsletterStatusRepository->getByEmail($subscriber->getUserName());
    }

    /**
     * @throws CustomerNotFound
     */
    public function createNewsletterUser(NewsletterStatusSubscribeType $input): CustomerDataType
    {
        /** @var EshopUserModel $user */
        $user = oxNew(EshopUserModel::class);

        $user->assign(
            [
                'oxactive' => 1,
                'oxrights' => 'user',
                'oxsal' => $input->salutation(),
                'oxfname' => $input->firstName(),
                'oxlname' => $input->lastName(),
                'oxusername' => $input->email(),
                'oxpassword' => '',
            ]
        );

        return $this->customerRepository->createUser($user);
    }

    private function setNewsSubscription(SubscriberDataType $subscriber, bool $flag): bool
    {
        $sendOptinMail = $this->legacyService->getConfigParam('blOrderOptInEmail');

        /** @var EshopNewsletterSubscriptionStatusModel $newsletterModel */
        $newsletterModel = $subscriber->getEshopModel()->getNewsSubscription();
        $newsletterModel->setOptInStatus(
            $newsletterModel->getRawFieldData('oxnewssubscribed__oxdboptin') ?: 0
        );

        return $subscriber->getEshopModel()->setNewsSubscription($flag, $sendOptinMail);
    }
}
