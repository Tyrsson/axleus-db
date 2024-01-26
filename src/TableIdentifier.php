<?php

declare(strict_types=1);

namespace Axleus\Db;

use Laminas\Db\Sql\TableIdentifier as Table;

final class TableIdentifier extends Table
{
    public function __construct(string $prefix, string $table, ?string $schema = null)
    {
        parent::__construct($prefix.$table, $schema);
    }
}