<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Review\Input;

interface ReviewInputInterface
{
    public function getProductId(): string;

    public function getText(): ?string;

    public function getRating(): ?int;
}
