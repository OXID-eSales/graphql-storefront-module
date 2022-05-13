<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType;

use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class NewsletterStatusSubscribe
{
    /** @var string */
    private $lastName;

    /** @var string */
    private $firstName;

    /** @var string */
    private $salutation;

    /** @var string */
    private $email;

    /** @var ?string */
    private $userId;

    public function __construct(
        string $firstName,
        string $lastName,
        string $salutation,
        string $email,
        ?string $userId
    ) {
        $this->salutation = $salutation;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->userId = $userId;
    }

    /**
     * @Field()
     */
    public function salutation(): string
    {
        return $this->salutation;
    }

    /**
     * @Field()
     */
    public function firstName(): string
    {
        return $this->firstName;
    }

    /**
     * @Field()
     */
    public function lastName(): string
    {
        return $this->lastName;
    }

    /**
     * @Field()
     */
    public function email(): string
    {
        return $this->email;
    }

    public function userId(): ?string
    {
        return $this->userId;
    }
}
