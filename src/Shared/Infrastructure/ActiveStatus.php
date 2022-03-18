<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Infrastructure;

use DateTimeInterface;
use OxidEsales\GraphQL\Base\DataType\DateTimeImmutableFactory;

trait ActiveStatus
{
    public function active(?DateTimeInterface $now = null): bool
    {
        $model = $this->getEshopModel();

        $active = (bool) $model->getRawFieldData('oxactive');

        if ($active) {
            return true;
        }

        $from = DateTimeImmutableFactory::fromString(
            (string) $model->getRawFieldData('oxactivefrom')
        );
        $to = DateTimeImmutableFactory::fromString(
            (string) $model->getRawFieldData('oxactiveto')
        );
        $now = $now ?? DateTimeImmutableFactory::fromString('now');

        if ($from <= $now && $to >= $now) {
            return true;
        }

        return false;
    }
}
