<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Shop;

use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\Eshop\Application\Model\UserBasketItem;
use OxidEsales\Eshop\Application\Model\Voucher;
use OxidEsales\Eshop\Core\Exception\ArticleException;
use OxidEsales\Eshop\Core\Exception\ObjectException as EshopObjectException;
use OxidEsales\Eshop\Core\Price as EshopPrice;

/**
 * Basket model extended
 *
 * @mixin Basket
 * @eshopExtension
 */
class Basket extends Basket_parent
{
    /**
     * Add product to basket without doing any check.
     */
    public function addProductToBasket(UserBasketItem $basketItem): void
    {
        $item                                              = $this->convertUserBasketItemToBasketItem($basketItem);
        $this->_aBasketContents[$item->getBasketItemKey()] = $item;
    }

    /**
     * check and apply or mark as not reserved the voucher by given ID.
     */
    public function applyVoucher(string $voucherId): void
    {
        /** @var \OxidEsales\GraphQL\Storefront\Shared\Shop\Voucher $voucher */
        $voucher = oxNew(Voucher::class);

        $voucher->load($voucherId);

        try {
            $voucher->isInReservationTimeLimit();
            $voucher->getSerie();
            $this->_aVouchers[$voucher->getId()] = $voucher->getSimpleVoucher();
        } catch (EshopObjectException $exception) {
            $voucher->unMarkAsReserved();
        }
    }

    public function getBasketDeliveryCost(): EshopPrice
    {
        return $this->calcDeliveryCost();
    }

    /**
     * Convert user basket item to basket item.
     */
    private function convertUserBasketItemToBasketItem(
        UserBasketItem $userBasketItem
    ): BasketItem {
        /** @var BasketItem $basketItem */
        $basketItem = oxNew(BasketItem::class);

        $stockCheckMode = $this->getStockCheckMode();
        $basketItem->setStockCheckStatus(false);

        try {
            $basketItem->init(
                $userBasketItem->getFieldData('oxartid'),
                $userBasketItem->getFieldData('oxamount'),
                $userBasketItem->getSelList(),
                $userBasketItem->getPersParams()
            );

            //Any basket object will do to generate the item key
            $itemKey = $this->getItemKey(
                $userBasketItem->getFieldData('oxartid'),
                $userBasketItem->getSelList(),
                $userBasketItem->getPersParams()
            );
            $basketItem->setBasketItemKey($itemKey);
        } catch (ArticleException $e) {
            // Ignore article exceptions
        }

        $basketItem->setStockCheckStatus($stockCheckMode);

        return $basketItem;
    }
}
