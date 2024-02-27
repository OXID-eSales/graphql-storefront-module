<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Infrastructure;

use OxidEsales\Eshop\Application\Model\Order as EshopOrderModel;
use OxidEsales\Eshop\Application\Model\OrderFileList as OrderFileListModel;
use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\Eshop\Core\Model\ListModel as EshopListModel;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination as PaginationFilter;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;
use OxidEsales\GraphQL\Storefront\Order\DataType\Order as OrderDataType;
use OxidEsales\GraphQL\Storefront\Order\DataType\OrderFile;

final class Customer
{
    /**
     * @return OrderDataType[]
     */
    public function getOrders(CustomerDataType $customer, ?PaginationFilter $pagination = null): array
    {
        $limit = false;
        $page = 0;

        if ($pagination) {
            $limit = 0 < (int)$pagination->limit() ? (int)$pagination->limit() : $limit;

            if ($limit) {
                $offset = (int)$pagination->offset();
                $page = (int)$offset / $limit;
            }
        }

        /** @var EshopUserModel $customerModel */
        $customerModel = $customer->getEshopModel();

        $ordersList = $customerModel->getOrders($limit, $page);

        $orders = [];
        foreach ($ordersList->getArray() as $orderModel) {
            /** @var EshopOrderModel $orderModel */
            $orders[] = new OrderDataType($orderModel);
        }

        return $orders;
    }

    public function getOrderFiles(CustomerDataType $customer): array
    {
        /** @var OrderFileListModel $orderFileList */
        $orderFileList = oxNew(OrderFileListModel::class);
        $orderFileList->loadUserFiles((string)$customer->getId());
        $result = [];

        $orderFiles = $orderFileList->getArray();
        if ($orderFiles) {
            foreach ($orderFiles as $orderFile) {
                $result[] = new OrderFile($orderFile);
            }
        }

        return $result;
    }

    public function getUserActiveCountryId(string $userId): string
    {
        /** @var EshopUserModel $userModel */
        $userModel = oxNew(EshopUserModel::class);
        $userModel->load($userId);

        return (string)$userModel->getActiveCountry();
    }
}
