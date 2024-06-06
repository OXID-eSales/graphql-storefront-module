<?php

namespace OxidEsales\GraphQL\Storefront\Shared\Infrastructure;

use InvalidArgumentException;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\GraphQL\Base\DataType\Pagination\Pagination as PaginationFilter;
use OxidEsales\GraphQL\Base\DataType\Sorting\Sorting as BaseSorting;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList;

interface RepositoryInterface
{
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
    public function getById(string $id, string $type, bool $disableSubShop = true);

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
    ): array;

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
    ): array;

    /**
     * @throws NotFound
     *
     * @return true
     */
    public function delete(BaseModel $item): bool;

    /**
     * @return true
     */
    public function saveModel(BaseModel $item): bool;
}
