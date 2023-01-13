<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Infrastructure;

use InvalidArgumentException;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\GraphQL\Base\DataType\Filter\FilterInterface;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination as PaginationFilter;
use OxidEsales\GraphQL\Base\DataType\ShopModelAwareInterface;
use OxidEsales\GraphQL\Base\DataType\Sorting\Sorting as BaseSorting;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList;
use OxidEsales\GraphQL\Storefront\Shared\Exception\Repository as RepositoryException;
use PDO;

final class Repository
{
    /** @var QueryBuilderFactoryInterface */
    private $queryBuilderFactory;

    /** @var ListConfiguration */
    private $listConfiguration;

    public function __construct(
        QueryBuilderFactoryInterface $queryBuilderFactory,
        ListConfiguration $listConfiguration
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->listConfiguration = $listConfiguration;
    }

    /**
     * @template T
     *
     * @param class-string<T> $type
     *
     * @throws InvalidArgumentException if $type is not instance of ShopModelAwareInterface
     * @throws NotFound                 if BaseModel can not be loaded
     *
     * @return T
     */
    public function getById(
        string $id,
        string $type,
        bool $disableSubShop = true
    ) {
        $model = $this->getModel($type::getModelClass(), $disableSubShop);

        if (!$model->load($id) || (method_exists($model, 'canView') && !$model->canView())) {
            throw new NotFound($id);
        }
        $type = new $type($model);

        if (!($type instanceof ShopModelAwareInterface)) {
            throw new InvalidArgumentException();
        }

        return $type;
    }

    /**
     * @template T
     *
     * @param class-string<T> $type
     *
     * @throws InvalidArgumentException if model in $type is not instance of BaseModel
     *
     * @return T[]
     */
    public function getList(
        string $type,
        FilterList $filter,
        PaginationFilter $pagination,
        BaseSorting $sorting,
        bool $disableSubShop = true
    ): array {

        $model = $this->getModel(
            $type::getModelClass(),
            $disableSubShop
        );
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->select($model->getViewName() . '.*')
            ->from($model->getViewName());

        if (
            $filter->getActive() !== null &&
            $filter->getActive()->equals() === true
        ) {
            $activeSnippet = $model->getSqlActiveSnippet();

            if (strlen($activeSnippet)) {
                $queryBuilder->andWhere($activeSnippet);
            }
        }

        /** @var FilterInterface[] $filters */
        $filters = array_filter($filter->getFilters());

        foreach ($filters as $field => $fieldFilter) {
            $fieldFilter->addToQuery($queryBuilder, $field);
        }

        $pagination->addPaginationToQuery($queryBuilder);

        $sorting->addToQuery($queryBuilder);

        return $this->getTypesList(
            $queryBuilder,
            $model,
            $type
        );
    }

    /**
     * @template T
     *
     * @param class-string<T> $type
     *
     * @throws InvalidArgumentException if model in $type is not instance of BaseModel
     *
     * @return T[]
     *
     * @deprecated use self::getList instead
     */
    public function getByFilter(
        FilterList $filter,
        string $type,
        ?PaginationFilter $pagination = null,
        bool $disableSubShop = true
    ): array {
        $model = $this->getModel($type::getModelClass(), $disableSubShop);

        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->select($model->getViewName() . '.*')
            ->from($model->getViewName())
            ->orderBy($model->getViewName() . '.oxid');

        if (
            $filter->getActive() !== null &&
            $filter->getActive()->equals() === true
        ) {
            $activeSnippet = $model->getSqlActiveSnippet();

            if (strlen($activeSnippet)) {
                $queryBuilder->andWhere($activeSnippet);
            }
        }

        /** @var FilterInterface[] $filters */
        $filters = array_filter($filter->getFilters());

        foreach ($filters as $field => $fieldFilter) {
            $fieldFilter->addToQuery($queryBuilder, $field);
        }

        if ($pagination !== null) {
            $pagination->addPaginationToQuery($queryBuilder);
        }

        return $this->getTypesList(
            $queryBuilder,
            $model,
            $type
        );
    }

    /**
     * @return true
     * @throws NotFound
     *
     */
    public function delete(BaseModel $item): bool
    {
        if (!$item->delete()) {
            throw RepositoryException::cannotDeleteModel();
        }

        return true;
    }

    /**
     * @return true
     */
    public function saveModel(BaseModel $item): bool
    {
        if (!$item->save()) {
            throw RepositoryException::cannotSaveModel();
        }

        return true;
    }

    /**
     * @param class-string $modelClass
     *
     * @throws InvalidArgumentException if model in $type is not instance of BaseModel
     */
    private function getModel(string $modelClass, bool $disableSubShop): BaseModel
    {
        $model = oxNew($modelClass);

        if (!($model instanceof BaseModel)) {
            throw new InvalidArgumentException();
        }

        if (method_exists($model, 'setDisableShopCheck')) {
            $model->setDisableShopCheck($disableSubShop);
        }

        if (method_exists($model, 'setLanguage')) {
            $model->setLanguage(Registry::getLang()->getBaseLanguage());
        }

        return $model;
    }

    /**
     * @template T
     *
     * @param class-string<T> $type
     *
     * @return T[]
     */
    private function getTypesList(
        QueryBuilder $queryBuilder,
        BaseModel $model,
        string $type
    ): array {
        $types = [];
        $loadListObjects = $this->listConfiguration->shouldLoadModel($model);

        $queryBuilder->getConnection()->setFetchMode(PDO::FETCH_ASSOC);

        /** @var \Doctrine\DBAL\Statement $result */
        $result = $queryBuilder->execute();

        foreach ($result as $row) {
            $newModel = clone $model;
            $newModel->assign($row);
            !$loadListObjects ?: $newModel->load($newModel->getId());
            $types[] = new $type($newModel);
        }

        return $types;
    }
}
