<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\WishedPrice\Infrastructure;

use OxidEsales\Eshop\Application\Model\PriceAlarm as WishedPriceModel;
use OxidEsales\Eshop\Core\Email;
use OxidEsales\GraphQL\Storefront\WishedPrice\DataType\WishedPrice as WishedPriceDataType;
use OxidEsales\GraphQL\Storefront\WishedPrice\Exception\NotificationSendFailure;
use OxidEsales\GraphQL\Storefront\WishedPrice\Factory\EmailFactoryInterface;
use OxidEsales\GraphQL\Storefront\WishedPrice\Infrastructure\WishedPriceNotification;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(WishedPriceNotification::class)]
final class WishedPriceNotificationTest extends TestCase
{
    #[Test]
    public function sendNotificationThrowsExceptionWhenEmailIsEmpty(): void
    {
        $wishedPrice = $this->createWishedPrice('');

        $emailFactoryStub = $this->createStub(EmailFactoryInterface::class);

        $sut = $this->getSut($emailFactoryStub);

        $this->expectException(NotificationSendFailure::class);
        $this->expectExceptionMessage('Email address cannot be empty');

        $sut->sendNotification($wishedPrice);
    }

    #[Test]
    public function sendsNotificationSuccessfully(): void
    {
        $userEmail = uniqid() . '@example.com';
        $productId = uniqid();

        $wishedPrice = $this->createWishedPrice($userEmail, $productId);
        $wishedPriceModelStub = $wishedPrice->getEshopModel();

        $emailSpy = $this->createMock(Email::class);
        $emailSpy->expects($this->once())
            ->method('sendPriceAlarmNotification')
            ->with(
                [
                    'aid' => $productId,
                    'email' => $userEmail,
                ],
                $wishedPriceModelStub
            )
            ->willReturn(true);

        $emailFactoryStub = $this->createStub(EmailFactoryInterface::class);
        $emailFactoryStub->method('create')->willReturn($emailSpy);

        $sut = $this->getSut($emailFactoryStub);

        $result = $sut->sendNotification($wishedPrice);

        $this->assertTrue($result);
    }

    #[Test]
    public function sendNotificationThrowsExceptionWhenSendingFails(): void
    {
        $userEmail = uniqid() . '@example.com';
        $productId = uniqid();
        $errorInfo = uniqid();

        $wishedPrice = $this->createWishedPrice($userEmail, $productId);

        $emailStub = $this->createStub(Email::class);
        $emailStub->method('sendPriceAlarmNotification')->willReturn(false);
        $emailStub->ErrorInfo = $errorInfo;

        $emailFactoryStub = $this->createStub(EmailFactoryInterface::class);
        $emailFactoryStub->method('create')->willReturn($emailStub);

        $sut = $this->getSut($emailFactoryStub);

        $this->expectException(NotificationSendFailure::class);
        $this->expectExceptionMessage($errorInfo);

        $sut->sendNotification($wishedPrice);
    }

    private function createWishedPrice(string $email, ?string $productId = null): WishedPriceDataType
    {
        $wishedPriceModelStub = $this->createStub(WishedPriceModel::class);
        $wishedPriceModelStub->method('getRawFieldData')->willReturnMap([
            ['oxemail', $email],
            ['oxartid', $productId ?? uniqid()],
        ]);

        return new WishedPriceDataType($wishedPriceModelStub);
    }

    private function getSut(EmailFactoryInterface $emailFactory): WishedPriceNotification
    {
        return new WishedPriceNotification($emailFactory);
    }
}
