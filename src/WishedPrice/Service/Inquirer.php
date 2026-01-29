<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Service;

use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\RepositoryInterface;
use OxidEsales\GraphQL\Storefront\WishedPrice\DataType\Inquirer as InquirerDataType;
use OxidEsales\GraphQL\Storefront\WishedPrice\Exception\InquirerNotFound;

final class Inquirer
{
    /** @var RepositoryInterface */
    private $repository;

    public function __construct(
        RepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @throws InquirerNotFound
     */
    public function inquirer(string $id): InquirerDataType
    {
        try {
            /** @var InquirerDataType $inquirer */
            $inquirer = $this->repository->getById($id, InquirerDataType::class);
        } catch (NotFound $e) {
            throw new InquirerNotFound($id);
        }

        return $inquirer;
    }
}
