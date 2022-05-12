<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType;

use OxidEsales\Eshop\Application\Model\NewsSubscribed as EshopNewsletterSubscriptionStatusModel;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class NewsletterStatusUnsubscribe implements ShopModelAwareInterface
{
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
    public function email(): string
    {
        return (string)$this->newsletterSubscriptionStatus->getRawFieldData('oxemail');
    }

    public function userId(): ID
    {
        return new ID(
            (string)$this->newsletterSubscriptionStatus->getRawFieldData('oxuserid')
        );
    }

    public static function getModelClass(): string
    {
        return EshopNewsletterSubscriptionStatusModel::class;
    }
}
