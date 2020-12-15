<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Tests\Unit\Shared\Infrastructure;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;
use PHPUnit\Framework\TestCase;

/**
 * @covers OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository
 */
final class RepositoryTest extends TestCase
{
    public function testFatalErrorOnWrongClassById(): void
    {
        $this->expectException(\Error::class);
        $repository = new Repository(
            $this->createMock(QueryBuilderFactoryInterface::class)
        );
        $repository->getById(
            'foo',
            \stdClass::class
        );
    }

    public function testExceptionOnWrongModelById(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $repository = new Repository(
            $this->createMock(QueryBuilderFactoryInterface::class)
        );
        $repository->getById(
            'foo',
            WrongType::class
        );
    }

    public function testExceptionOnWrongTypeById(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $repository = new Repository(
            $this->createMock(QueryBuilderFactoryInterface::class)
        );
        $repository->getById(
            'foo',
            AlsoWrongType::class
        );
    }

    public function testExceptionOnWrongModelByFilter(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $repository = new Repository(
            $this->createMock(QueryBuilderFactoryInterface::class)
        );
        $repository->getByFilter(
            new EmptyFilterList(),
            WrongType::class
        );
    }

    public function testExceptionOnFailToDeleteModel(): void
    {
        $this->expectException(\RuntimeException::class);
        $repository = new Repository(
            $this->createMock(QueryBuilderFactoryInterface::class)
        );
        $repository->delete(new CorrectModel());
    }

    public function testModelSave(): void
    {
        $repository = new Repository(
            $this->createMock(QueryBuilderFactoryInterface::class)
        );

        $model = $this->createPartialMock(
            \OxidEsales\Eshop\Core\Model\BaseModel::class,
            ['save']
        );
        $model->expects($this->any())->method('save')->willReturn('someid');
        $this->assertTrue($repository->saveModel($model));
    }

    public function testModelSaveFailed(): void
    {
        $this->expectException(\RuntimeException::class);
        $repository = new Repository(
            $this->createMock(QueryBuilderFactoryInterface::class)
        );

        $model = $this->createPartialMock(
            \OxidEsales\Eshop\Core\Model\BaseModel::class,
            ['save']
        );
        $model->expects($this->any())->method('save')->willReturn(false);
        $this->assertTrue($repository->saveModel($model));
    }
}

// phpcs:disable

final class EmptyFilterList extends \OxidEsales\GraphQL\Storefront\Shared\DataType\FilterList
{
    public function getFilters(): array
    {
        return [];
    }
}

final class WrongModel
{
}

final class WrongType
{
    public static function getModelClass(): string
    {
        return WrongModel::class;
    }
}

final class CorrectModel extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    public function __construct()
    {
    }

    public function load($oxid)
    {
        return true;
    }

    public function delete($oxid = null)
    {
        return false;
    }
}

final class AlsoWrongType
{
    public static function getModelClass(): string
    {
        return CorrectModel::class;
    }
}

namespace OxidEsales\GraphQL\Storefront\Shared\Infrastructure;

if (!function_exists("\oxNew")) {
    function oxNew(string $class)
    {
        return new $class();
    }
}
