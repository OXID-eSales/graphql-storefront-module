<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Infrastructure;

use OxidEsales\Eshop\Core\Model\BaseModel;

/**
 * List objects are not loaded with BaseModel::load() by default.
 * In case this is necessary for some list objects, please override this service in the
 * configurable_services.yaml. Specify objects with which core table should be fully loaded in lists,
 *
 * Example:
 * OxidEsales\GraphQL\Storefront\Shared\Infrastructure\ListConfiguration:
 *   class: OxidEsales\GraphQL\Storefront\Shared\Infrastructure\ListConfiguration
 *   arguments:
 *        $map: { oxcategories: 'oxcategories' }
 *   public: true
 */
final class ListConfiguration
{
    /**
     * @var array
     */
    private $map;

    public function __construct(array $map = [])
    {
        $this->map = $map;
    }

    public function getMap(): array
    {
        return $this->map;
    }

    public function shouldLoadModel(BaseModel $model): bool
    {
        return isset($this->getMap()[$model->getCoreTableName()]);
    }
}
