<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Event;

use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use Symfony\Component\EventDispatcher\Event;

final class CreateAnonymousUser extends Event
{
    public const NAME = self::class;

    /** @var EshopUserModel */
    private $user;

    /** @var string */
    private $id;

    /**
     * CreateAnonymousUser constructor.
     */
    public function __construct(EshopUserModel $user, string $id)
    {
        $this->user = $user;
        $this->id   = $id;
    }

    public function getUser(): EshopUserModel
    {
        return $this->user;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
