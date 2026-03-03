<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\WishedPrice\Infrastructure;

use OxidEsales\Eshop\Application\Model\PriceAlarm as WishedPriceModel;
use OxidEsales\Eshop\Core\Email;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\OxNewFactoryInterface;
use OxidEsales\GraphQL\Storefront\WishedPrice\DataType\WishedPrice as WishedPriceDataType;
use OxidEsales\GraphQL\Storefront\WishedPrice\Exception\NotificationSendFailure;
use OxidEsales\GraphQL\Storefront\WishedPrice\Infrastructure\WishedPriceNotification;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(WishedPriceNotification::class)]
final class WishedPriceNotificationTest extends TestCase
{
    #[Test]
    public function sendNotificationThrowsExceptionWhenEmailIsEmpty(): void
    {
        $wishedPrice = $this->createWishedPrice('');

        $oxNewFactoryStub = $this->createStub(OxNewFactoryInterface::class);

        $sut = $this->getSut($oxNewFactoryStub);

        $this->expectException(NotificationSendFailure::class);
        $this->expectExceptionMessage('Email address cannot be empty');

        $sut->sendNotification($wishedPrice);
    }

    #[Test]
    public function sendsNotificationSuccessfully(): void
    {
        $userEmail = uniqid() . '@oxid-esales.com';
        $productId = uniqid();

        $wishedPrice = $this->createWishedPrice($userEmail, $productId);
        $wishedPriceModelStub = $wishedPrice->getEshopModel();

        $emailMock = $this->createMock(Email::class);
        $emailMock->expects($this->once())
            ->method('sendPriceAlarmNotification')
            ->with(
                [
                    'aid' => $productId,
                    'email' => $userEmail,
                ],
                $wishedPriceModelStub
            )
            ->willReturn(true);

        $oxNewFactoryStub = $this->createStub(OxNewFactoryInterface::class);
        $oxNewFactoryStub->method('getModel')->willReturn($emailMock);

        $sut = $this->getSut($oxNewFactoryStub);

        $result = $sut->sendNotification($wishedPrice);

        $this->assertTrue($result);
    }

    #[Test]
    public function sendNotificationThrowsExceptionWhenSendingFails(): void
    {
        $userEmail = uniqid() . '@oxid-esales.com';
        $productId = uniqid();
        $errorInfo = uniqid();

        $wishedPrice = $this->createWishedPrice($userEmail, $productId);

        $emailStub = $this->createStub(Email::class);
        $emailStub->method('sendPriceAlarmNotification')->willReturn(false);
        $emailStub->ErrorInfo = $errorInfo;

        $oxNewFactoryStub = $this->createStub(OxNewFactoryInterface::class);
        $oxNewFactoryStub->method('getModel')->willReturn($emailStub);

        $sut = $this->getSut($oxNewFactoryStub);

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

    private function getSut(OxNewFactoryInterface $oxNewFactory): WishedPriceNotification
    {
        return new WishedPriceNotification($oxNewFactory);
    }
}
