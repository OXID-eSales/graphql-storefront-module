<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Basket\Infrastructure;

use Doctrine\DBAL\FetchMode;
use OxidEsales\Eshop\Application\Model\UserBasket as UserBasketEshopModel;
use OxidEsales\Eshop\Core\Registry as EshopRegistry;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket as BasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketNotFound;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository as SharedRepository;
use PDO;
use TheCodingMachine\GraphQLite\Types\ID;
use function getViewName;

final class Repository
{
    /** @var SharedRepository */
    private $sharedRepository;

    /** @var QueryBuilderFactoryInterface */
    private $queryBuilderFactory;

    public function __construct(
        SharedRepository $sharedRepository,
        QueryBuilderFactoryInterface $queryBuilderFactory
    ) {
        $this->sharedRepository    = $sharedRepository;
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    /**
     * @throws BasketNotFound
     */
    public function getBasketById(string $id): BasketDataType
    {
        try {
            /** @var BasketDataType $basket */
            $basket = $this->sharedRepository->getById(
                $id,
                BasketDataType::class,
                false
            );
        } catch (NotFound $e) {
            throw BasketNotFound::byId($id);
        }

        return $basket;
    }

    /**
     * @throws BasketNotFound
     */
    public function customerBasketByTitle(CustomerDataType $customer, string $title): BasketDataType
    {
        $model = $customer->getEshopModel()->getBasket($title);

        if (!$model->getId()) {
            throw BasketNotFound::byOwnerAndTitle(
                (string) $customer->getId(),
                $title
            );
        }

        return new BasketDataType($model);
    }

    /**
     * @throws BasketNotFound
     *
     * @return BasketDataType[]
     */
    public function customerBaskets(CustomerDataType $customer): array
    {
        $baskets   = [];
        $basketIds = $this->getCustomerBasketIds($customer->getId());

        foreach ($basketIds as $basketId) {
            $baskets[] = $this->sharedRepository->getById(
                $basketId,
                BasketDataType::class,
                false
            );
        }

        return $baskets;
    }

    /**
     * @return BasketDataType[]
     */
    public function publicBasketsByOwnerNameOrEmail(string $search): array
    {
        $baskets = [];

        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->select('userbaskets.*')
                     ->from(getViewName('oxuserbaskets'), 'userbaskets')
                     ->innerJoin('userbaskets', getViewName('oxuser'), 'users', 'users.oxid = userbaskets.oxuserid')
                     ->where('userbaskets.oxpublic = 1')
                     ->andWhere("userbaskets.OXTITLE != 'savedbasket'")
                     ->andWhere("userbaskets.OXTITLE != 'noticelist'")
                     ->andWhere('(users.oxusername = :search OR users.oxlname = :search)')
                     ->setParameters([
                         ':search' => $search,
                     ]);

        if (!EshopRegistry::getConfig()->getConfigParam('blMallUsers')) {
            $queryBuilder->andWhere('(users.oxshopid = :shopId)')
                         ->setParameter(':shopId', EshopRegistry::getConfig()->getShopId());
        }

        $queryBuilder->getConnection()->setFetchMode(PDO::FETCH_ASSOC);
        /** @var \Doctrine\DBAL\Statement $result */
        $result = $queryBuilder->execute();

        /** @var UserBasketEshopModel */
        $model = oxNew(UserBasketEshopModel::class);

        foreach ($result as $row) {
            $newModel = clone $model;
            $newModel->assign($row);
            $baskets[] = new BasketDataType($newModel);
        }

        return $baskets;
    }

    private function getCustomerBasketIds(ID $customerId): array
    {
        $queryBuilder = $this->queryBuilderFactory->create();

        /** @var \Doctrine\DBAL\Driver\Statement $execute */
        $execute =  $queryBuilder
            ->select('oxid')
            ->from(getViewName('oxuserbaskets'), 'userbaskets')
            ->where('oxuserid = :customerId')
            ->setParameter(':customerId', $customerId)
            ->execute();

        return $execute->fetchAll(FetchMode::COLUMN);
    }
}
