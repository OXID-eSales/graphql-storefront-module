<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Infrastructure;

use OxidEsales\Eshop\Application\Model\Address as EshopAddressModel;
use OxidEsales\Eshop\Application\Model\Basket as EshopBasketModel;
use OxidEsales\Eshop\Application\Model\DeliveryList as EshopDeliveryListModel;
use OxidEsales\Eshop\Application\Model\DeliverySet as EshopDeliverySetModel;
use OxidEsales\Eshop\Application\Model\DeliverySetList as EshopDeliverySetListModel;
use OxidEsales\Eshop\Application\Model\Order as OrderModel;

use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use OxidEsales\Eshop\Application\Model\UserBasket as EshopUserBasketModel;
use OxidEsales\Eshop\Application\Model\UserBasketItem as EshopUserBasketItemModel;
use OxidEsales\Eshop\Core\Registry as EshopRegistry;
use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket as BasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketItemNotFound;
use OxidEsales\GraphQL\Storefront\Basket\Exception\PlaceOrder as PlaceOrderException;
use OxidEsales\GraphQL\Storefront\Country\DataType\Country as CountryDataType;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;
use OxidEsales\GraphQL\Storefront\DeliveryMethod\DataType\BasketDeliveryMethod as BasketDeliveryMethodDataType;
use OxidEsales\GraphQL\Storefront\Order\DataType\Order as OrderDataType;
use OxidEsales\GraphQL\Storefront\Payment\DataType\BasketPayment;
use OxidEsales\GraphQL\Storefront\Shared\DataType\Price as PriceDataType;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Basket as SharedBasketInfrastructure;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;

final class Basket
{
    /** @var Repository */
    private $repository;

    /** @var SharedBasketInfrastructure */
    private $sharedBasketInfrastructure;

    public function __construct(
        Repository $repository,
        SharedBasketInfrastructure $sharedBasketInfrastructure
    ) {
        $this->repository                 = $repository;
        $this->sharedBasketInfrastructure = $sharedBasketInfrastructure;
    }

    public function addBasketItem(BasketDataType $basket, string $productId, float $amount): bool
    {
        $model = $basket->getEshopModel();
        $model->addItemToBasket($productId, $amount);

        return true;
    }

    public function removeBasketItem(BasketDataType $basket, string $basketItemId, float $amount): bool
    {
        $model      = $basket->getEshopModel();
        $basketItem = $this->getBasketItem($basket->getEshopModel(), $basketItemId);

        if (!($basketItem instanceof EshopUserBasketItemModel)) {
            throw BasketItemNotFound::byId($basketItemId, $model->getId());
        }

        $amountRemaining = (float) $basketItem->getFieldData('oxamount') - $amount;

        if ($amountRemaining <= 0 || $amount == 0) {
            $amountRemaining = 0;
        }

        $productId = (string) $basketItem->getFieldData('oxartid');
        $params    = $basketItem->getFieldData('oxperparams');

        $model->addItemToBasket($productId, $amountRemaining, null, true, $params);

        $this->sharedBasketInfrastructure->getCalculatedBasket($basket);

        return true;
    }

    public function makePublic(BasketDataType $basket): bool
    {
        $model = $basket->getEshopModel();
        $model->assign([
            'oxuserbaskets__oxpublic' => 1,
        ]);

        return $this->repository->saveModel($model);
    }

    public function makePrivate(BasketDataType $basket): bool
    {
        $model = $basket->getEshopModel();
        $model->assign([
            'oxuserbaskets__oxpublic' => 0,
        ]);

        return $this->repository->saveModel($model);
    }

    public function setDeliveryAddress(BasketDataType $basket, string $deliveryAddressId): bool
    {
        $model = $basket->getEshopModel();

        $model->assign([
            'OEGQL_DELADDRESSID' => $deliveryAddressId,
        ]);

        return $this->repository->saveModel($model);
    }

    public function setPayment(BasketDataType $basket, string $paymentId): bool
    {
        $model = $basket->getEshopModel();

        $model->assign([
            'OEGQL_PAYMENTID' => $paymentId,
        ]);

        return $this->repository->saveModel($model);
    }

    /**
     * Update delivery method id for user basket
     * Resets payment id as it may be not available for new delivery method
     */
    public function setDeliveryMethod(BasketDataType $basket, string $deliveryMethodId): bool
    {
        $model = $basket->getEshopModel();

        $model->assign([
            'OEGQL_DELIVERYMETHODID' => $deliveryMethodId,
            'OEGQL_PAYMENTID'        => '',
        ]);

        return $this->repository->saveModel($model);
    }

    /**
     * @return BasketDeliveryMethodDataType[]
     */
    public function getBasketAvailableDeliveryMethods(
        CustomerDataType $customer,
        BasketDataType $userBasket,
        CountryDataType $country
    ): array {
        $userModel   = $customer->getEshopModel();
        $basketModel = $this->sharedBasketInfrastructure->getCalculatedBasket($userBasket);

        //Initialize available delivery set list for user and country
        /** @var EshopDeliverySetListModel $deliverySetList */
        $deliverySetList      = oxNew(EshopDeliverySetListModel::class);
        $deliverySetListArray = $deliverySetList->getDeliverySetList($userModel, (string) $country->getId());

        $result = [];
        /** @var EshopDeliverySetModel $deliverySet */
        foreach ($deliverySetListArray as $setKey => $deliverySet) {
            [$allMethods, $activeShipSet, $paymentList] = $deliverySetList->getDeliverySetData(
                $setKey,
                $userModel,
                /** @phpstan-ignore-next-line */
                $basketModel
            );

            $deliveryMethodPayments = [];

            foreach ($paymentList as $paymentModel) {
                $deliveryMethodPayments[$paymentModel->getId()] = new BasketPayment($paymentModel, $basketModel);
            }

            if (!empty($deliveryMethodPayments)) {
                $result[$setKey] = new BasketDeliveryMethodDataType($deliverySet, $basketModel, $deliveryMethodPayments);
            }
        }

        return $result;
    }

    public function placeOrder(
        CustomerDataType $customer,
        BasketDataType $userBasket
    ): OrderDataType {

        /** @var EshopUserModel $userModel */
        $userModel = $customer->getEshopModel();

        /** @var EshopUserBasketModel $userBasketModel */
        $userBasketModel = $userBasket->getEshopModel();

        if ($userBasketModel->getItemCount() === 0) {
            throw PlaceOrderException::emptyBasket((string) $userBasket->id());
        }

        $_POST['sDeliveryAddressMD5'] = $userModel->getEncodedDeliveryAddress();

        //set delivery address to basket if any is given
        if (!empty($userBasketModel->getFieldData('oegql_deladdressid'))) {
            $userModel->setSelectedAddressId($userBasketModel->getFieldData('oegql_deladdressid'));
            $_POST['deladrid'] = $userModel->getSelectedAddressId();
            /** @var EshopAddressModel $deliveryAdress */
            $deliveryAdress    = oxNew(EshopAddressModel::class);
            $deliveryAdress->load($userModel->getSelectedAddressId());
            $_POST['sDeliveryAddressMD5'] .= $deliveryAdress->getEncodedDeliveryAddress();
        }

        /** @var EshopBasketModel $basketModel */
        $basketModel = $this->sharedBasketInfrastructure->getCalculatedBasket($userBasket);
        EshopRegistry::getSession()->setBasket($basketModel);

        /** @var OrderModel $orderModel */
        $orderModel = oxNew(OrderModel::class);
        $status     = $orderModel->finalizeOrder($basketModel, $userModel);

        // performing special actions after user finishes order (assignment to special user groups)
        $userModel->onOrderExecute($basketModel, $status);

        //we need to delete the basket after order to prevent ordering it twice
        if ($status === $orderModel::ORDER_STATE_OK || $status === $orderModel::ORDER_STATE_MAILINGERROR) {
            $userBasketModel->delete();
        } else {
            throw PlaceOrderException::byBasketId($userBasketModel->getId(), (string) $status);
        }

        //return order data type
        return new OrderDataType($orderModel);
    }

    public function getDeliveryPrice(BasketDeliveryMethodDataType $basketDeliveryMethod): PriceDataType
    {
        $basketModel = $basketDeliveryMethod->getBasketModel();
        $basketModel->setShipping($basketDeliveryMethod->getEshopModel()->getId());
        $basketModel->onUpdate();
        $basketModel->calculateBasket();

        //Reset delivery list otherwise wrong cost will be displayed
        EshopRegistry::set(EshopDeliveryListModel::class, null);

        return new PriceDataType(
            $basketModel->getDeliveryCost()
        );
    }

    private function getBasketItem(EshopUserBasketModel $model, string $basketItemId): ?EshopUserBasketItemModel
    {
        $basketItems = $model->getItems();
        /** @var EshopUserBasketItemModel $item */
        foreach ($basketItems as $item) {
            if ($item->getId() === $basketItemId) {
                return $item;
            }
        }

        return null;
    }
}
