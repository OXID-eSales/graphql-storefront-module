<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Catalogue\Vendor\Controller;

use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Catalogue\Vendor\DataType\Sorting;
use OxidEsales\GraphQL\Catalogue\Vendor\DataType\Vendor as VendorDataType;
use OxidEsales\GraphQL\Catalogue\Vendor\DataType\VendorFilterList;
use OxidEsales\GraphQL\Catalogue\Vendor\Exception\VendorNotFound;
use OxidEsales\GraphQL\Catalogue\Vendor\Service\Vendor as VendorService;
use TheCodingMachine\GraphQLite\Annotations\Query;

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
    public function vendor(string $id): VendorDataType
    {
        return $this->vendorService->vendor($id);
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
