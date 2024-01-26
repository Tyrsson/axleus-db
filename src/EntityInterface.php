<?php

/**
 * This interface does not extend the Permission Acl interfaces so
 * that developers can implement them via the concrete classes
 */

declare(strict_types=1);

namespace Axleus\Db;

interface EntityInterface
{
    public function getId(): array|int|string|null;
}
