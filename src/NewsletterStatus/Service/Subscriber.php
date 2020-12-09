<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\NewsletterStatus\Service;

use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\DataType\Subscriber as SubscriberDataType;
use OxidEsales\GraphQL\Storefront\NewsletterStatus\Exception\SubscriberNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;

final class Subscriber
{
    /** @var Repository */
    private $repository;

    /** @var Legacy */
    private $legacyService;

    public function __construct(
        Repository $repository,
        Legacy $legacyService
    ) {
        $this->repository    = $repository;
        $this->legacyService = $legacyService;
    }

    /**
     * @throws SubscriberNotFound
     */
    public function subscriber(string $id): SubscriberDataType
    {
        $ignoreSubshop = (bool) $this->legacyService->getConfigParam('blMallUsers');

        try {
            /** @var SubscriberDataType $subscriber */
            $subscriber = $this->repository->getById($id, SubscriberDataType::class, $ignoreSubshop);
        } catch (NotFound $e) {
            throw SubscriberNotFound::byId($id);
        }

        return $subscriber;
    }
}
