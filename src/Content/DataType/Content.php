<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Content\DataType;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Application\Model\Content as EshopContentModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use TheCodingMachine\GraphQLite\Types\ID;

/**
 * @Type()
 */
final class Content implements ShopModelAwareInterface
{
    public const TYPE_CATEGORY = 2;

    /** @var EshopContentModel */
    private $content;

    public function __construct(
        EshopContentModel $content
    ) {
        $this->content = $content;
    }

    public function getEshopModel(): EshopContentModel
    {
        return $this->content;
    }

    /**
     * @Field()
     */
    public function getId(): ID
    {
        return new ID($this->content->getId());
    }

    /**
     * @Field()
     */
    public function isActive(): bool
    {
        return (bool) $this->content->isActive();
    }

    /**
     * @Field()
     */
    public function getTitle(): string
    {
        return $this->content->getTitle();
    }

    /**
     * Returns rendered HTML string that might contain script and style tags
     *
     * @Field()
     */
    public function getContent(): string
    {
        $oActView = oxNew(FrontendController::class);
        $oActView->addGlobalParams();

        /** @var \OxidEsales\Eshop\Core\UtilsView $oUtilsView */
        $oUtilsView = Registry::getUtilsView();

        return $oUtilsView->getRenderedContent(
            $this->content->getRawFieldData('oxcontent'),
            $oActView->getViewData(),
            $this->content->getId()
        );
    }

    /**
     * Return not rendered, raw content
     *
     * @Field()
     */
    public function getRawContent(): string
    {
        return $this->content->getRawFieldData('oxcontent');
    }

    /**
     * @Field()
     */
    public function getFolder(): string
    {
        return $this->content->getRawFieldData('oxfolder');
    }

    /**
     * @Field()
     */
    public function getVersion(): string
    {
        return $this->content->getRawFieldData('oxtermversion');
    }

    /**
     * @return class-string
     */
    public static function getModelClass(): string
    {
        return EshopContentModel::class;
    }
}
