<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\DataType;

use Doctrine\DBAL\Query\QueryBuilder;
use InvalidArgumentException;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\GraphQL\Base\DataType\FilterInterface;
use OxidEsales\GraphQL\Base\Infrastructure\Legacy as LegacyService;
use TheCodingMachine\GraphQLite\Annotations\Factory;

final class SeoSlugFilter implements FilterInterface
{
    /** @var string */
    private $like;

    /** @var string */
    private $type = 'static';

    private $prefix = '%';

    private $postfix = '%';

    public function __construct(string $like)
    {
        $this->like = $like;
    }

    public function like(): string
    {
        return $this->like;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function unsetPrefix(): void
    {
        $this->prefix = '';
    }

    public function unsetPostfix(): void
    {
        $this->postfix = '';
    }

    public function addToQuery(QueryBuilder $builder, string $field): void
    {
        $from = $builder->getQueryPart('from');

        if ($from === []) {
            throw new InvalidArgumentException('QueryBuilder is missing "from" SQL part');
        }
        $table = $from[0]['alias'] ?? $from[0]['table'];

        $viewname = $alias = 'oxseo';
        $language = $this->getLanguageId();

        $builder
            ->join(
                $table,
                $viewname,
                $alias,
                $builder->expr()->eq("$table.OXID", "$alias.OXOBJECTID")
            )
            ->andWhere($builder->expr()->eq($alias . '.OXTYPE', ':type'))
            ->andWhere($builder->expr()->like('LOWER(' . $alias . '.OXSEOURL)', "LOWER(:$field)"))
            ->andWhere($builder->expr()->eq($alias . '.OXLANG', ':lang'))
            ->setParameter(':type', $this->type())
            ->setParameter(":$field", $this->prefix . $this->like() . $this->postfix)
            ->setParameter(':lang', $language);
    }

    private function getLanguageId(): int
    {
        $container = ContainerFactory::getInstance()->getContainer();

        return $container->get(LegacyService::class)->getLanguageId();
    }

    /**
     * @Factory(name="SeoSlugFilterInput")
     */
    public static function fromUserInput(string $like): self
    {
        return new self($like);
    }
}
