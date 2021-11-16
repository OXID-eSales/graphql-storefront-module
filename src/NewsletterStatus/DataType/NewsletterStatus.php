<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType;

use DateTimeInterface;
use OxidEsales\Eshop\Application\Model\NewsSubscribed as EshopNewsletterSubscriptionStatusModel;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;
use OxidEsales\GraphQL\Storefront\Shared\DataType\DataType;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class NewsletterStatus implements DataType
{
    private const STATUS_0 = 'UNSUBSCRIBED';

    private const STATUS_1 = 'SUBSCRIBED';

    private const STATUS_2 = 'MISSING_DOUBLE_OPTIN';

    private const DEFAULT_STATUS = 0;

    /** @var array */
    private $statusMapping = [
        0 => self::STATUS_0,
        1 => self::STATUS_1,
        2 => self::STATUS_2,
    ];

    /** @var EshopNewsletterSubscriptionStatusModel */
    private $newsletterSubscriptionStatus;

    public function __construct(
        EshopNewsletterSubscriptionStatusModel $newsletterSubscriptionStatus
    ) {
        $this->newsletterSubscriptionStatus = $newsletterSubscriptionStatus;
    }

    public function getEshopModel(): EshopNewsletterSubscriptionStatusModel
    {
        return $this->newsletterSubscriptionStatus;
    }

    /**
     * @Field()
     */
    public function salutation(): string
    {
        return (string) $this->newsletterSubscriptionStatus->getRawFieldData('oxsal');
    }

    /**
     * @Field()
     */
    public function firstName(): string
    {
        return (string) $this->newsletterSubscriptionStatus->getRawFieldData('oxfname');
    }

    /**
     * @Field()
     */
    public function lastName(): string
    {
        return (string) $this->newsletterSubscriptionStatus->getRawFieldData('oxlname');
    }

    /**
     * @Field()
     */
    public function email(): string
    {
        return (string) $this->newsletterSubscriptionStatus->getRawFieldData('oxemail');
    }

    /**
     * @Field()
     */
    public function status(): string
    {
        $status = $this->newsletterSubscriptionStatus->getOptInStatus();

        if (!array_key_exists($status, $this->statusMapping)) {
            $status = self::DEFAULT_STATUS;
        }

        return $this->statusMapping[$status];
    }

    /**
     * @Field()
     */
    public function failedEmailCount(): int
    {
        return (int) $this->newsletterSubscriptionStatus->getRawFieldData('oxemailfailed');
    }

    /**
     * @Field()
     */
    public function subscribed(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->newsletterSubscriptionStatus->getRawFieldData('oxsubscribed')
        );
    }

    /**
     * @Field()
     */
    public function unsubscribed(): ?DateTimeInterface
    {
        $dateTime = (string) $this->newsletterSubscriptionStatus->getRawFieldData('oxunsubscribed');

        return DateTimeImmutableFactory::fromString(
            $dateTime
        );
    }

    /**
     * @Field()
     */
    public function updated(): ?DateTimeInterface
    {
        return DateTimeImmutableFactory::fromString(
            (string) $this->newsletterSubscriptionStatus->getRawFieldData('oxtimestamp')
        );
    }

    public function userId(): ID
    {
        return new ID(
            (string) $this->newsletterSubscriptionStatus->getRawFieldData('oxuserid')
        );
    }

    public static function getModelClass(): string
    {
        return EshopNewsletterSubscriptionStatusModel::class;
    }
}
