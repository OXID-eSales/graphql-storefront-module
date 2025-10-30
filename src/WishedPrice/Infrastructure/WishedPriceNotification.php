<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Infrastructure;

use OxidEsales\Eshop\Core\Email;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\OxNewFactoryInterface;
use OxidEsales\GraphQL\Storefront\WishedPrice\DataType\WishedPrice as WishedPriceDataType;
use OxidEsales\GraphQL\Storefront\WishedPrice\Exception\NotificationSendFailure;

final class WishedPriceNotification
{
    public function __construct(
        private readonly OxNewFactoryInterface $oxNewFactory
    ) {
    }

    public function sendNotification(WishedPriceDataType $wishedPrice): bool
    {
        $userEmail = $wishedPrice->getEmail();

        if (empty($userEmail)) {
            throw NotificationSendFailure::create('Email address cannot be empty');
        }

        $email = $this->oxNewFactory->getModel(Email::class);

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
