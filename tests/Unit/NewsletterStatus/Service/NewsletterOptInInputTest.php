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
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Service\SubscriberServiceInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TheCodingMachine\GraphQLite\Types\ID;

#[CoversClass(NewsletterOptInInput::class)]
final class NewsletterOptInInputTest extends TestCase
{
    #[Test]
    public function fromUserInputReturnsNewsletterStatusWhenConfirmationCodeMatches(): void
    {
        $email = uniqid();
        $userId = new ID(uniqid());
        $username = uniqid();
        $passSalt = uniqid();

        $legacyMock = $this->createMock(Legacy::class);
        $legacyMock->method('isValidEmail')
            ->with($email)
            ->willReturn(true);

        $newsletterStatusModelStub = $this->createStub(EshopNewsletterSubscriptionStatusModel::class);
        $newsletterStatusModelStub->method('getRawFieldData')->willReturnMap([
            ['oxuserid', $userId],
        ]);
        $newsletterStatus = new NewsletterStatusType($newsletterStatusModelStub);

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->method('getByEmail')
            ->with($email)
            ->willReturn($newsletterStatus);

        $subscriberModelStub = $this->createStub(EshopUserModel::class);
        $subscriberModelStub->method('getRawFieldData')->willReturnMap([
            ['oxusername', $username],
            ['oxpasssalt', $passSalt],
        ]);

        $subscriberServiceMock = $this->createMock(SubscriberServiceInterface::class);
        $subscriberServiceMock->method('subscriber')
            ->with((string)$userId)
            ->willReturn(new SubscriberDataType($subscriberModelStub));

        $sut = $this->getSut(
            subscriberService: $subscriberServiceMock,
            newsletterStatusRepository: $repositoryMock,
            legacyService: $legacyMock,
        );

        $this->assertSame($newsletterStatus, $sut->fromUserInput($email, md5($username . $passSalt)));
    }

    #[Test]
    public function fromUserInputThrowsInvalidEmailForMalformedEmail(): void
    {
        $email = uniqid();

        $legacyMock = $this->createMock(Legacy::class);
        $legacyMock->method('isValidEmail')
            ->with($email)
            ->willReturn(false);

        $repositorySpy = $this->createMock(RepositoryInterface::class);
        $repositorySpy->expects($this->never())->method('getByEmail');

        $sut = $this->getSut(newsletterStatusRepository: $repositorySpy, legacyService: $legacyMock);

        $this->expectExceptionObject(InvalidEmail::byString($email));

        $sut->fromUserInput($email, uniqid());
    }

    #[Test]
    public function fromUserInputThrowsInvalidEmailWhenNewsletterStatusNotFound(): void
    {
        $email = uniqid();
        $confirmCode = uniqid();

        $legacyMock = $this->createMock(Legacy::class);
        $legacyMock->method('isValidEmail')
            ->with($email)
            ->willReturn(true);

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->method('getByEmail')
            ->with($email)
            ->willThrowException(new NewsletterStatusNotFound($email));

        $sut = $this->getSut(
            newsletterStatusRepository: $repositoryMock,
            legacyService: $legacyMock,
        );

        $this->expectExceptionObject(InvalidEmail::byConfirmationCode($confirmCode));

        $sut->fromUserInput($email, $confirmCode);
    }

    #[Test]
    public function fromUserInputThrowsInvalidEmailWhenSubscriberNotFound(): void
    {
        $email = uniqid();
        $userId = uniqid();
        $confirmCode = uniqid();

        $legacyMock = $this->createMock(Legacy::class);
        $legacyMock->method('isValidEmail')
            ->with($email)
            ->willReturn(true);

        $newsletterStatusModelStub = $this->createStub(EshopNewsletterSubscriptionStatusModel::class);
        $newsletterStatusModelStub->method('getRawFieldData')->willReturnMap([
            ['oxuserid', $userId],
        ]);

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->method('getByEmail')
            ->with($email)
            ->willReturn(new NewsletterStatusType($newsletterStatusModelStub));

        $subscriberServiceMock = $this->createMock(SubscriberServiceInterface::class);
        $subscriberServiceMock->method('subscriber')
            ->with($userId)
            ->willThrowException(new SubscriberNotFound($userId));

        $sut = $this->getSut(
            subscriberService: $subscriberServiceMock,
            newsletterStatusRepository: $repositoryMock,
            legacyService: $legacyMock,
        );

        $this->expectExceptionObject(InvalidEmail::byConfirmationCode($confirmCode));

        $sut->fromUserInput($email, $confirmCode);
    }

    #[Test]
    public function fromUserInputThrowsInvalidEmailWhenConfirmationCodeDoesNotMatch(): void
    {
        $email = uniqid();
        $userId = uniqid();
        $wrongConfirmCode = uniqid();

        $legacyMock = $this->createMock(Legacy::class);
        $legacyMock->method('isValidEmail')
            ->with($email)
            ->willReturn(true);

        $newsletterStatusModelStub = $this->createStub(EshopNewsletterSubscriptionStatusModel::class);
        $newsletterStatusModelStub->method('getRawFieldData')->willReturnMap([
            ['oxuserid', $userId],
        ]);

        $repositoryMock = $this->createMock(RepositoryInterface::class);
        $repositoryMock->method('getByEmail')
            ->with($email)
            ->willReturn(new NewsletterStatusType($newsletterStatusModelStub));

        $subscriberModelStub = $this->createStub(EshopUserModel::class);
        $subscriberModelStub->method('getRawFieldData')->willReturnMap([
            ['oxusername', uniqid()],
            ['oxpasssalt', uniqid()],
        ]);

        $subscriberServiceMock = $this->createMock(SubscriberServiceInterface::class);
        $subscriberServiceMock->method('subscriber')
            ->with($userId)
            ->willReturn(new SubscriberDataType($subscriberModelStub));

        $sut = $this->getSut(
            subscriberService: $subscriberServiceMock,
            newsletterStatusRepository: $repositoryMock,
            legacyService: $legacyMock,
        );

        $this->expectExceptionObject(InvalidEmail::byConfirmationCode($wrongConfirmCode));

        $sut->fromUserInput($email, $wrongConfirmCode);
    }

    private function getSut(
        ?SubscriberServiceInterface $subscriberService = null,
        ?RepositoryInterface $newsletterStatusRepository = null,
        ?Legacy $legacyService = null,
    ): NewsletterOptInInput {
        return new NewsletterOptInInput(
            subscriberService: $subscriberService ?? $this->createStub(SubscriberServiceInterface::class),
            newsletterStatusRepository: $newsletterStatusRepository ?? $this->createStub(RepositoryInterface::class),
            legacyService: $legacyService ?? $this->createStub(Legacy::class),
        );
    }
}
