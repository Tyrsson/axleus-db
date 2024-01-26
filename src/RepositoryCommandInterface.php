<?php

declare(strict_types=1);

namespace Axleus\Db;

interface RepositoryCommandInterface
{
    /**
     * Returns either a EntityInterface populated with the persisted data
     * Or an int representing \Laminas\Db\AbstractTableGateway::getLastInsertValue()
     */
    public function save(EntityInterface $entity): EntityInterface|int;
    public function delete(EntityInterface $entity): int;
}
