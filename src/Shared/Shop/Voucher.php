<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Shop;

use OxidEsales\Eshop\Application\Model\Basket as EshopBasketModel;
use OxidEsales\Eshop\Application\Model\Discount as EshopDiscountModel;
use OxidEsales\Eshop\Core\Exception\ObjectException as EshopObjectException;
use OxidEsales\GraphQL\Storefront\Basket\Service\Basket as BasketService;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Basket as SharedBasketInfrastructure;

/**
 * Voucher model extended
 *
 * @mixin Voucher
 * @eshopExtension
 */
final class Voucher extends Voucher_parent
{
    public function unMarkAsReserved(): void
    {
        parent::unMarkAsReserved();

        if ($this->getId()) {
            $this->load($this->getId());
            $this->assign(
                [
                    'oegql_basketid' => '',
                ]
            );
            $this->save();
        }
    }

    /**
     * @throws EshopObjectException
     */
    public function isInReservationTimeLimit(): void
    {
        if ((0 < $this->getFieldData('oxreserved')) &&
            ($this->getFieldData('oxreserved') < (time() - $this->_getVoucherTimeout()))) {
            throw new EshopObjectException('Reservation has timed out');
        }
    }

    public function isProductVoucher(): bool
    {
        return $this->_isProductVoucher();
    }

    public function isCategoryVoucher(): bool
    {
        return $this->_isCategoryVoucher();
    }

    public function getSerieDiscount(): EshopDiscountModel
    {
        return $this->_getSerieDiscount();
    }

    protected function _getBasketItems($oDiscount = null): array
    {
        $items = parent::_getBasketItems($oDiscount);

        if (empty($items)) {
            $items = $this->getGraphQLBasketItems($oDiscount);
        }

        return $items;
    }

    /**
     * This method is the same as _getSessionBasketItems, the difference is in the $oBasket.
     * In GraphQL module we don't have session, that's why we need to get GraphQL basket.
     *
     * @param null|mixed $oDiscount
     */
    protected function getGraphQLBasketItems($oDiscount = null): array
    {
        if (null === $oDiscount) {
            $oDiscount = $this->_getSerieDiscount();
        }

        /** Here is the difference with _getSessionBasketItems method */
        $oBasket = $this->getGraphQLBasket();
        $aItems  = [];
        $iCount  = 0;

        foreach ($oBasket->getContents() as $oBasketItem) {
            if (!$oBasketItem->isDiscountArticle() && ($oArticle = $oBasketItem->getArticle()) && !$oArticle->skipDiscounts() && $oDiscount->isForBasketItem($oArticle)) {
                $aItems[$iCount] = [
                    'oxid'     => $oArticle->getId(),
                    'price'    => $oArticle->getBasketPrice($oBasketItem->getAmount(), $oBasketItem->getSelList(), $oBasket)->getPrice(),
                    'discount' => $oDiscount->getAbsValue($oArticle->getBasketPrice($oBasketItem->getAmount(), $oBasketItem->getSelList(), $oBasket)->getPrice()),
                    'amount'   => $oBasketItem->getAmount(),
                ];

                $iCount++;
            }
        }

        return $aItems;
    }

    protected function getGraphQLBasket(): EshopBasketModel
    {
        $basketModel = oxNew(EshopBasketModel::class);
        $basketId    = $this->getFieldData('oegql_basketid');

        if ($basketId) {
            /** @var BasketService $basketService */
            $basketService = $this->getContainer()->get(BasketService::class);
            $basket        = $basketService->basket($basketId);

            /** @var SharedBasketInfrastructure $sharedBasketInfrastructure */
            $sharedBasketInfrastructure = $this->getContainer()->get(SharedBasketInfrastructure::class);
            $basketModel                = $sharedBasketInfrastructure->getBasket($basket);
        }

        return $basketModel;
    }
}
