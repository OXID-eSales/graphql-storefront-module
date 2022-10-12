<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Service;

//use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleDependencyServiceInterface;

final class ModuleDependencyService //implements ModuleDependencyServiceInterface
{
    public function getModuleId(): string
    {
        return 'oe_graphql_storefront';
    }

    public function getDependencies(): array
    {
        return  [
            'oe_graphql_base' => 'oe_graphql_storefront'
        ];
    }

}
