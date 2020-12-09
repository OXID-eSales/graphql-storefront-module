<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Contact\DataType;

final class ContactRequest
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $salutation;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $message;

    public function __construct(
        string $email,
        string $firstName,
        string $lastName,
        string $salutation,
        string $subject,
        string $message
    ) {
        $this->email      = $email;
        $this->firstName  = $firstName;
        $this->lastName   = $lastName;
        $this->salutation = $salutation;
        $this->subject    = $subject;
        $this->message    = $message;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string[]
     */
    public function getFields(): array
    {
        return [
            'email'      => $this->email,
            'firstName'  => $this->firstName,
            'lastName'   => $this->lastName,
            'salutation' => $this->salutation,
            'subject'    => $this->subject,
            'message'    => $this->message,
        ];
    }
}
