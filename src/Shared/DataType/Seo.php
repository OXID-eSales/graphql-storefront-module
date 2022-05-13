<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\DataType;

use OxidEsales\Eshop\Core\Contract\IUrl as EshopContractUrl;
use OxidEsales\Eshop\Core\Model\BaseModel as EshopModel;
use OxidEsales\Eshop\Core\Registry as EshopRegistry;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type()
 */
final class Seo
{
    /** @var EshopModel */
    private $eshopModel;

    public function __construct(
        EshopModel $eshopModel
    ) {
        $this->eshopModel = $eshopModel;
    }

    /**
     * @Field()
     */
    public function getDescription(): string
    {
        return $this->getMetaData('oxdescription');
    }

    /**
     * @Field()
     */
    public function getKeywords(): string
    {
        return $this->getMetaData('oxkeywords');
    }

    /**
     * @Field()
     */
    public function getUrl(): ?string
    {
        if ($this->eshopModel instanceof EshopContractUrl) {
            return $this->eshopModel->getLink();
        }

        return null;
    }

    private function getMetaData(string $metaType): string
    {
        return (string)EshopRegistry::getSeoEncoder()
            ->getMetaData(
                $this->eshopModel->getId(),
                $metaType
            );
    }
}
