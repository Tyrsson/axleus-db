<?php

declare(strict_types=1);

namespace Axleus\Db;

use Laminas\Db\ResultSet\ResultSetInterface;

interface RepositoryInterface
{
    public function findOneById(int $id): EntityInterface;
    public function findOneByColumn(string $column, int|string $value): ResultSetInterface|EntityInterface;
    public function findManyByColumn(array $titles): ResultSetInterface|array;
    public function fetchAll(): ResultSetInterface|array;
}
