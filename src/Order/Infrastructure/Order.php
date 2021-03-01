<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Order\Infrastructure;

use Iterator;
use OxidEsales\Eshop\Application\Model\OrderArticle;
use OxidEsales\Eshop\Application\Model\OrderFileList as OrderFileListModel;
use OxidEsales\Eshop\Application\Model\UserPayment as EshopUserPaymentModel;
use OxidEsales\Eshop\Application\Model\VoucherList as EshopVoucherListModel;
use OxidEsales\GraphQL\Storefront\Order\DataType\Order as OrderDataType;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderDelivery as OrderDeliveryDataType;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderDeliveryAddress;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderFile;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderInvoiceAddress;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderItem;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderPayment;
use OxidEsales\GraphQL\Storefront\Shared\Shop\Voucher as EshopVoucherModel;
use OxidEsales\GraphQL\Storefront\Voucher\DataType\Voucher;

final class Order
{
    public function deliveryAddress(OrderDataType $order): ?OrderDeliveryAddress
    {
        $result    = new OrderDeliveryAddress($order->getEshopModel());
        $countryId = (string) $result->countryId();

        if (empty($countryId)) {
            $result = null;
        }

        return $result;
    }

    public function invoiceAddress(OrderDataType $order): OrderInvoiceAddress
    {
        return new OrderInvoiceAddress($order->getEshopModel());
    }

    public function delivery(OrderDataType $order): OrderDeliveryDataType
    {
        return new OrderDeliveryDataType($order->getEshopModel());
    }

    /**
     * @return Voucher[]
     */
    public function getOrderVouchers(OrderDataType $order): array
    {
        /** @var EshopVoucherListModel $list */
        $list = oxNew(EshopVoucherListModel::class);
        $list->selectString(
            'select * from oxvouchers where oxorderid = :orderId',
            ['orderId' => $order->getId()]
        );

        /** @var EshopVoucherModel[] $voucherModels */
        $voucherModels = $list->getArray();

        $usedVouchers = [];

        foreach ($voucherModels as $oneVoucher) {
            $usedVouchers[] = new Voucher($oneVoucher);
        }

        return $usedVouchers;
    }

    public function getOrderItems(OrderDataType $order): array
    {
        /** @var Iterator<OrderArticle> $orderArticles */
        $orderArticles = $order->getEshopModel()->getOrderArticles();
        $items         = [];

        foreach ($orderArticles as $oneArticle) {
            $items[] = new OrderItem($oneArticle);
        }

        return $items;
    }

    public function getOrderPayment(OrderDataType $order): ?OrderPayment
    {
        /** @var EshopUserPaymentModel|false $payment */
        $payment = $order->getEshopModel()->getPaymentType();

        return $payment ? new OrderPayment($payment) : null;
    }

    public function getOrderFiles(OrderDataType $order): array
    {
        /** @var OrderFileListModel $orderFileList */
        $orderFileList = oxNew(OrderFileListModel::class);
        $orderFileList->loadOrderFiles((string) $order->getId());
        $result = [];

        if ($orderFiles = $orderFileList->getArray()) {
            foreach ($orderFiles as $orderFile) {
                $result[] = new OrderFile($orderFile);
            }
        }

        return $result;
    }
}
