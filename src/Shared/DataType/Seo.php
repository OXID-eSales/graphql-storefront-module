<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\DataType;

use OxidEsales\Eshop\Core\Contract\IUrl  as EshopContractUrl;
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
        return (string) EshopRegistry::getSeoEncoder()
            ->getMetaData(
                $this->eshopModel->getId(),
                'oxdescription'
            );
    }

    /**
     * @Field()
     */
    public function getKeywords(): string
    {
        return (string) EshopRegistry::getSeoEncoder()
            ->getMetaData(
                $this->eshopModel->getId(),
                'oxkeywords'
            );
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

    /**
     * @Field()
     */
    public function getSlug(): ?string
    {
        if (method_exists($this->eshopModel, 'getBaseSeoLink')) {
            $seoLink = $this->eshopModel->getBaseSeoLink(null);
            $path = parse_url($seoLink, PHP_URL_PATH);
            $tmp = explode(DIRECTORY_SEPARATOR, rtrim($path, DIRECTORY_SEPARATOR));
            return array_pop($tmp);
        }

        return null;
    }
}
