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
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Base\DataType\Filter\IDFilter;
use OxidEsales\GraphQL\Base\DataType\Filter\StringFilter;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination as PaginationFilter;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Basket\DataType\Basket as BasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\DataType\BasketByTitleAndUserIdFilterList;
use OxidEsales\GraphQL\Storefront\Basket\DataType\PublicBasket as PublicBasketDataType;
use OxidEsales\GraphQL\Storefront\Basket\DataType\Sorting;
use OxidEsales\GraphQL\Storefront\Basket\Exception\BasketNotFound;
use OxidEsales\GraphQL\Storefront\Customer\DataType\Customer as CustomerDataType;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository as SharedRepository;
use PDO;
use TheCodingMachine\GraphQLite\Types\ID;

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

    public function basketExistsByTitleAndUserId(string $title, ID $userId): bool
    {
        $filter = new BasketByTitleAndUserIdFilterList(
            new StringFilter($title),
            new IDFilter($userId)
        );

        $fromDb = $this->sharedRepository->getList(
            BasketDataType::class,
            $filter,
            new PaginationFilter(),
            new Sorting()
        );

        return (bool) count($fromDb);
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
     * @return PublicBasketDataType[]
     */
    public function publicBasketsByOwnerNameOrEmail(string $search): array
    {
        $baskets                = [];
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);

        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->select('userbaskets.*')
        ->from($tableViewNameGenerator->getViewName('oxuserbaskets'), 'userbaskets')
        ->innerJoin('userbaskets', $tableViewNameGenerator->getViewName('oxuser'), 'users', 'users.oxid = userbaskets.oxuserid')
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
            $baskets[] = new PublicBasketDataType($newModel);
        }

        return $baskets;
    }

    private function getCustomerBasketIds(ID $customerId): array
    {
        $queryBuilder           = $this->queryBuilderFactory->create();
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);

        /** @var \Doctrine\DBAL\Driver\Statement $execute */
        $execute =  $queryBuilder
            ->select('oxid')
            ->from($tableViewNameGenerator->getViewName('oxuserbaskets'), 'userbaskets')
            ->where('oxuserid = :customerId')
            ->setParameter(':customerId', $customerId)
            ->execute();

        return $execute->fetchAll(FetchMode::COLUMN);
    }
}
