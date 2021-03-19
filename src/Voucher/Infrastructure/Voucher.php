<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Voucher\Infrastructure;

use Exception;
use OxidEsales\Eshop\Application\Model\Basket as EshopBasketModel;
use OxidEsales\Eshop\Core\Exception\ObjectException as EshopObjectException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\TransactionService as EshopDatabaseTransactionService;
use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket as BasketDataType;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Basket as SharedBasketInfrastructure;
use OxidEsales\GraphQL\Storefront\Shared\Shop\Voucher as StorefrontVoucherModel;
use OxidEsales\GraphQL\Storefront\Voucher\DataType\Voucher as VoucherDataType;
use OxidEsales\GraphQL\Storefront\Voucher\Exception\VoucherNotApplied;
use OxidEsales\GraphQL\Storefront\Voucher\Exception\VoucherNotFound;
use OxidEsales\GraphQL\Storefront\Voucher\Exception\VoucherNotUsable;

final class Voucher
{
    /** @var Repository */
    private $repository;

    /** @var SharedBasketInfrastructure */
    private $sharedBasketInfrastructure;

    /** @var EshopDatabaseTransactionService */
    private $transactionService;

    public function __construct(
        Repository $repository,
        SharedBasketInfrastructure $sharedBasketInfrastructure,
        EshopDatabaseTransactionService $transactionService
    ) {
        $this->repository                 = $repository;
        $this->sharedBasketInfrastructure = $sharedBasketInfrastructure;
        $this->transactionService         = $transactionService;
    }

    public function addVoucher(
        VoucherDataType $voucher,
        BasketDataType $basket
    ): void {
        $this->transactionService->begin();

        try {
            $basketModel = $this->sharedBasketInfrastructure->getCalculatedBasket($basket);

            $activeVouchers = $this->repository->getBasketVouchers((string) $basket->id());
            $voucherModel   = $voucher->getEshopModel();
            $voucherModel->getVoucherByNr(
                $voucher->voucher(),
                $this->getActiveVouchersIds(($activeVouchers)),
                true
            );

            $voucherModel->checkVoucherAvailability(
                $this->getActiveVouchersNumbers($activeVouchers),
                $this->getProductsPrice($basketModel)
            );
            $voucherModel->checkUserAvailability($basket->getEshopModel()->getUser());
            $voucherModel->markAsReserved();

            $this->repository->addBasketIdToVoucher($basket->id(), $voucherModel->getId());
        } catch (Exception $exception) {
            $this->transactionService->rollback();

            throw VoucherNotUsable::withMessage($exception->getMessage());
        }
        $this->transactionService->commit();
    }

    public function removeVoucher(
        VoucherDataType $voucherDataType,
        BasketDataType $userBasket
    ): void {
        $voucherId      = (string) $voucherDataType->id();
        $activeVouchers = $this->repository->getBasketVouchers((string) $userBasket->id());

        if (in_array($voucherId, $this->getActiveVouchersIds($activeVouchers))) {
            $voucherModel = $voucherDataType->getEshopModel();
            $voucherModel->load($voucherId);
            $voucherModel->unMarkAsReserved();
            $this->repository->removeBasketIdFromVoucher($voucherId);
        } else {
            throw VoucherNotApplied::byId($voucherId, (string) $userBasket->id());
        }
    }

    public function isVoucherSerieUsableInCurrentShop(VoucherDataType $voucherDataType): bool
    {
        $result = true;

        try {
            $voucherDataType->getEshopModel()->getSerie();
        } catch (EshopObjectException $exception) {
            $result = false;
        }

        return $result;
    }

    /**
     * @throws VoucherNotFound
     */
    public function checkProductAvailability(BasketDataType $basket, VoucherDataType $voucher): void
    {
        /** @var StorefrontVoucherModel $voucherModel */
        $voucherModel = $voucher->getEshopModel();

        if (!$voucherModel->isProductVoucher()) {
            return;
        }

        $this->checkForVoucherRelatedProducts($basket, $voucher);
    }

    /**
     * @throws VoucherNotFound
     */
    public function checkCategoryAvailability(BasketDataType $basket, VoucherDataType $voucher): void
    {
        /** @var StorefrontVoucherModel $voucherModel */
        $voucherModel = $voucher->getEshopModel();

        if (!$voucherModel->isCategoryVoucher()) {
            return;
        }

        $this->checkForVoucherRelatedProducts($basket, $voucher);
    }

    /**
     * @throws VoucherNotFound
     */
    private function checkForVoucherRelatedProducts(BasketDataType $basket, VoucherDataType $voucher): void
    {
        /** @var StorefrontVoucherModel $voucherModel */
        $voucherModel = $voucher->getEshopModel();

        $discountModel = $voucherModel->getSerieDiscount();
        $basketModel   = $this->sharedBasketInfrastructure->getBasket($basket);
        $items         = $basketModel->getContents();

        $productIsInBasket = false;

        foreach ($items as $item) {
            $product = $item->getArticle();

            if (!$item->isDiscountArticle() &&
                $product &&
                !$product->skipDiscounts() &&
                $discountModel->isForBasketItem($product)
            ) {
                $productIsInBasket = true;

                break;
            }
        }

        if (!$productIsInBasket) {
            throw VoucherNotUsable::noProductsMessage();
        }
    }

    private function getActiveVouchersIds(array $activeVouchers): array
    {
        $ids = [];

        foreach ($activeVouchers as $activeVoucher) {
            if ($activeVoucher instanceof VoucherDataType) {
                $ids[] = (string) $activeVoucher->id();
            }
        }

        return $ids;
    }

    private function getProductsPrice(EshopBasketModel $eshopBasketModel): float
    {
        $productsPrice = 0;

        /** @var \OxidEsales\Eshop\Core\PriceList $productsList */
        $productsList = $eshopBasketModel->getDiscountProductsPrice();

        if ($productsList != null) {
            $productsPrice = $productsList->getSum($eshopBasketModel->isCalculationModeNetto());
        }

        return $productsPrice;
    }

    private function getActiveVouchersNumbers(array $activeVouchers): array
    {
        $vouchersNr = [];

        foreach ($activeVouchers as $activeVoucher) {
            if ($activeVoucher instanceof VoucherDataType) {
                $vouchersNr[(string) $activeVoucher->id()] = (string) $activeVoucher->number();
            }
        }

        return $vouchersNr;
    }
}
