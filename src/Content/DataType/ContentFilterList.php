<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Content\DataType;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\GraphQL\Base\DataType\Filter\StringFilter;
use OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList;
use OxidEsales\GraphQL\Storefront\Shared\DataType\SeoSlugFilter;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class ContentFilterList extends FilterList
{
    /** @var ?StringFilter */
    private $folder;

    /** @var StringFilter */
    private $shopid;

    /** @var ?SeoSlugFilter */
    private $slug;

    public function __construct(
        ?StringFilter $folder = null,
        ?SeoSlugFilter $slug = null
    ) {
        $this->folder = $folder;
        $container    = ContainerFactory::getInstance()->getContainer();
        $this->shopid = new StringFilter((string) $container->get(ContextInterface::class)->getCurrentShopId());
        $this->slug   = $slug;
        null === $this->slug ?: $this->slug->setType('oxcontent');

        parent::__construct();
    }

    /**
     * @return array{
     *                oxfolder: ?StringFilter,
     *                oxshopid: StringFilter,
     *                oxseourl: ?SeoSlugFilter,
     *                }
     */
    public function getFilters(): array
    {
        return [
            'oxfolder'   => $this->folder,
            'oxshopid'   => $this->shopid,
            'oxseourl'   => $this->slug,
        ];
    }

    /**
     * @Factory(name="ContentFilterList", default=true)
     */
    public static function createContentFilterList(
        ?StringFilter $folder = null,
        ?SeoSlugFilter $slug = null
    ): self {
        return new self(
            $folder,
            $slug
        );
    }
}
