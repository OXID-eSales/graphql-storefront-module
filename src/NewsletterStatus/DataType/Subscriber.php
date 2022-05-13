<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType;

use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class Subscriber implements ShopModelAwareInterface
{
    /** @var EshopUserModel */
    private $subscriber;

    public function __construct(EshopUserModel $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    public function getEshopModel(): EshopUserModel
    {
        return $this->subscriber;
    }

    public function getId(): string
    {
        return (string)$this->subscriber->getRawFieldData('oxid');
    }

    /**
     * @Field
     */
    public function getUserName(): string
    {
        return (string)$this->subscriber->getRawFieldData('oxusername');
    }

    public function getConfirmationCode(): string
    {
        return (string)md5($this->getUserName() . $this->subscriber->getRawFieldData('oxpasssalt'));
    }

    public static function getModelClass(): string
    {
        return EshopUserModel::class;
    }
}
