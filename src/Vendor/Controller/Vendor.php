<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Vendor\Controller;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Storefront\Vendor\DataType\Sorting;
use OxidEsales\GraphQL\Storefront\Vendor\DataType\Vendor as VendorDataType;
use OxidEsales\GraphQL\Storefront\Vendor\DataType\VendorFilterList;
use OxidEsales\GraphQL\Storefront\Vendor\Exception\VendorNotFound;
use OxidEsales\GraphQL\Storefront\Vendor\Service\Vendor as VendorService;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Types\ID;

final class Vendor
{
    /** @var VendorService */
    private $vendorService;

    public function __construct(
        VendorService $vendorService
    ) {
        $this->vendorService = $vendorService;
    }

    /**
     * @Query()
     *
     * @throws VendorNotFound
     * @throws InvalidLogin
     */
    public function vendor(?ID $vendorId, ?string $slug): VendorDataType
    {
        return $this->vendorService->vendor($vendorId, $slug);
    }

    /**
     * @Query()
     *
     * @return VendorDataType[]
     */
    public function vendors(
        ?VendorFilterList $filter = null,
        ?Sorting $sort = null
    ): array {
        return $this->vendorService->vendors(
            $filter ?? new VendorFilterList(),
            $sort ?? new Sorting([])
        );
    }
}
