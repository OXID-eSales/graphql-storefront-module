<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Category\DataType;

use Doctrine\DBAL\Query\QueryBuilder;
use InvalidArgumentException;
use OxidEsales\Eshop\Application\Model\Object2Category;
use OxidEsales\GraphQL\Base\DataType\FilterInterface;
use TheCodingMachine\GraphQLite\Annotations\Factory;
use TheCodingMachine\GraphQLite\Types\ID;

use function strtoupper;

final class CategoryIDFilter implements FilterInterface
{
    /** @var ID */
    private $equals;

    public function __construct(ID $equals)
    {
        $this->equals = $equals;
    }

    public function equals(): ID
    {
        return $this->equals;
    }

    public function addToQuery(QueryBuilder $builder, string $field): void
    {
        $from = $builder->getQueryPart('from');

        if ($from === []) {
            throw new InvalidArgumentException('QueryBuilder is missing "from" SQL part');
        }
        $table = $from[0]['alias'] ?? $from[0]['table'];

        /** @var Object2Category $model */
        $model = oxNew(Object2Category::class);
        $alias = $model->getViewName();

        $builder
            ->join(
                $table,
                $model->getViewName(),
                $alias,
                $builder->expr()->eq("$table.OXID", "$alias.OXOBJECTID")
            )
            ->andWhere($builder->expr()->eq($alias . '.' . strtoupper($field), ":$field"))
            ->setParameter(":$field", $this->equals());
    }

    /**
     * @Factory(name="CategoryIDFilterInput", default=true)
     */
    public static function fromUserInput(ID $equals): self
    {
        return new self($equals);
    }
}
