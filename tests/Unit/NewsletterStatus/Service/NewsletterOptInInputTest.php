<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\NewsletterStatus\Service;

use OxidEsales\Eshop\Application\Model\NewsSubscribed as EshopNewsletterSubscriptionStatusModel;
use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Storefront\Customer\Exception\InvalidEmail;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\NewsletterStatus as NewsletterStatusType;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\Subscriber as SubscriberDataType;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Exception\NewsletterStatusNotFound;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Exception\SubscriberNotFound;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Infrastructure\RepositoryInterface;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Service\NewsletterOptInInput;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Service\SubscriberInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(NewsletterOptInInput::class)]
final class NewsletterOptInInputTest extends TestCase
{
    #[Test]
    public function returnsNewsletterStatusWhenConfirmationCodeMatches(): void
    {
        $username = uniqid();
        $passSalt = uniqid();
        $newsletterStatus = $this->newsletterStatus(uniqid());

        $repository = $this->createStub(RepositoryInterface::class);
        $repository->method('getByEmail')->willReturn($newsletterStatus);

        $subscriberService = $this->createStub(SubscriberInterface::class);
        $subscriberService->method('subscriber')->willReturn($this->subscriber($username, $passSalt));

        $result = $this->getSut(subscriberService: $subscriberService, repository: $repository)
            ->fromUserInput(uniqid(), md5($username . $passSalt));

        $this->assertSame($newsletterStatus, $result);
    }

    #[Test]
    public function throwsInvalidEmailForMalformedAddress(): void
    {
        $this->expectException(InvalidEmail::class);

        $this->getSut(validEmail: false)->fromUserInput(uniqid(), uniqid());
    }

    #[Test]
    public function throwsWrongConfirmationCodeForUnknownEmail(): void
    {
        $email = uniqid();
        $confirmCode = uniqid();

        $repository = $this->createStub(RepositoryInterface::class);
        $repository->method('getByEmail')->willThrowException(new NewsletterStatusNotFound($email));

        $this->expectWrongConfirmationCode($confirmCode);

        $this->getSut(repository: $repository)->fromUserInput($email, $confirmCode);
    }

    #[Test]
    public function throwsWrongConfirmationCodeWhenSubscriberMissing(): void
    {
        $confirmCode = uniqid();

        $repository = $this->createStub(RepositoryInterface::class);
        $repository->method('getByEmail')->willReturn($this->newsletterStatus(uniqid()));

        $subscriberService = $this->createStub(SubscriberInterface::class);
        $subscriberService->method('subscriber')->willThrowException(new SubscriberNotFound(uniqid()));

        $this->expectWrongConfirmationCode($confirmCode);

        $this->getSut(subscriberService: $subscriberService, repository: $repository)
            ->fromUserInput(uniqid(), $confirmCode);
    }

    #[Test]
    public function throwsWrongConfirmationCodeForMismatchedCode(): void
    {
        $wrongCode = uniqid();

        $repository = $this->createStub(RepositoryInterface::class);
        $repository->method('getByEmail')->willReturn($this->newsletterStatus(uniqid()));

        $subscriberService = $this->createStub(SubscriberInterface::class);
        $subscriberService->method('subscriber')->willReturn($this->subscriber(uniqid(), uniqid()));

        $this->expectWrongConfirmationCode($wrongCode);

        $this->getSut(subscriberService: $subscriberService, repository: $repository)
            ->fromUserInput(uniqid(), $wrongCode);
    }

    private function expectWrongConfirmationCode(string $code): void
    {
        $this->expectException(InvalidEmail::class);
        $this->expectExceptionMessage(sprintf("Wrong e-mail confirmation code '%s'!", $code));
    }

    private function newsletterStatus(string $userId): NewsletterStatusType
    {
        return new NewsletterStatusType(
            $this->createConfiguredStub(EshopNewsletterSubscriptionStatusModel::class, [
                'getRawFieldData' => $userId,
            ])
        );
    }

    private function subscriber(string $username, string $passSalt): SubscriberDataType
    {
        $model = $this->createStub(EshopUserModel::class);
        $model->method('getRawFieldData')->willReturnMap([
            ['oxusername', $username],
            ['oxpasssalt', $passSalt],
        ]);

        return new SubscriberDataType($model);
    }

    private function getSut(
        ?SubscriberInterface $subscriberService = null,
        ?RepositoryInterface $repository = null,
        bool $validEmail = true,
    ): NewsletterOptInInput {
        $legacy = $this->createStub(Legacy::class);
        $legacy->method('isValidEmail')->willReturn($validEmail);

        return new NewsletterOptInInput(
            $subscriberService ?? $this->createStub(SubscriberInterface::class),
            $repository ?? $this->createStub(RepositoryInterface::class),
            $legacy,
        );
    }
}
