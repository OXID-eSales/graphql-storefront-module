<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Infrastructure;

use OxidEsales\GraphQL\Storefront\WishedPrice\DataType\WishedPrice as WishedPriceDataType;
use OxidEsales\GraphQL\Storefront\WishedPrice\Exception\NotificationSendFailure;
use OxidEsales\GraphQL\Storefront\WishedPrice\Factory\EmailFactoryInterface;

final class WishedPriceNotification
{
    public function __construct(
        private readonly EmailFactoryInterface $emailFactory
    ) {
    }

    public function sendNotification(WishedPriceDataType $wishedPrice): bool
    {
        $userEmail = $wishedPrice->getEmail();

        // TODO: Once OXID core fixes ValueError handling in Email::sendMail() (catches Throwable),
        //       consider if this empty email validation is still needed for this specific purpose.
        //       However, keeping it is still good practice for early validation.
        //       Background: PHP 8.4+ throws ValueError in idn_to_ascii() for invalid email addresses
        //       which bypasses Email::sendMail() catch block that only catches Exception.
        //       See: vendor/oxid-esales/oxideshop-ce/source/Core/Email.php:1767
        if (empty($userEmail)) {
            throw NotificationSendFailure::create('Email address cannot be empty');
        }

        $email = $this->emailFactory->create();

        $result = $email->sendPriceAlarmNotification(
            [
                'aid' => $wishedPrice->getProductId()->val(),
                'email' => $userEmail,
            ],
            $wishedPrice->getEshopModel()
        );

        if (!$result) {
            throw NotificationSendFailure::create($email->ErrorInfo);
        }

        return true;
    }
}
