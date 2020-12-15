<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\NewsletterStatus\Controller;

use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\NewsletterStatus as NewsletterStatusType;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\NewsletterStatusSubscribe as NewsletterStatusSubscribeType;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\NewsletterStatusUnsubscribe as NewsletterStatusUnsubscribeType;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Service\NewsletterStatus as NewsletterStatusService;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class NewsletterStatus
{
    /** @var NewsletterStatusService */
    private $newsletterStatusService;

    public function __construct(
        NewsletterStatusService $newsletterStatusService
    ) {
        $this->newsletterStatusService = $newsletterStatusService;
    }

    /**
     * @Mutation()
     */
    public function newsletterOptIn(NewsletterStatusType $newsletterStatus): NewsletterStatusType
    {
        $this->newsletterStatusService->optIn($newsletterStatus);

        return $newsletterStatus;
    }

    /**
     * NewsletterStatusUnsubscribeInput email field is optional.
     * In case of missing input email but available token, newsletter will be unsubscribed for token email.
     * Input email is preferred over token email.
     *
     * @Mutation()
     */
    public function newsletterUnsubscribe(
        ?NewsletterStatusUnsubscribeType $newsletterStatus
    ): bool {
        return $this->newsletterStatusService->unsubscribe($newsletterStatus);
    }

    /**
     * NewsletterStatusSubscribeInput input fields are optional in case of token.
     * - If token exists without NewsletterStatusSubscribeInput, token email will be subscribed.
     *   If token user is already subscribed, status will not be changed and no optin mail is sent.
     * - If token and NewsletterStatusSubscribeInput exists, input email will be subscribed.
     *   If input email user is already subscribed, status will be changed to 2 and
     *   optin mail is sent depending on shop config parameter blOrderOptInEmail.
     * - If only NewsletterStatusSubscribeInput exists, input email will be subscribed.
     *   If input email user is already subscribed, status will be changed to 2 and
     *   optin mail is sent depending on shop config parameter blOrderOptInEmail.
     *
     * If user account for email and shop exists, input fields are overruled by existing user data.
     * If user account for email and shop does not exist, new user will be created (no password, mininal data)
     *
     * @Mutation()
     */
    public function newsletterSubscribe(
        NewsletterStatusSubscribeType $newsletterStatus
    ): NewsletterStatusType {
        return $this->newsletterStatusService->subscribe($newsletterStatus);
    }
}
