<?php

declare(strict_types=1);

namespace Axleus\Db;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\TableIdentifier;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\TableGateway\Exception\RuntimeException;
use Laminas\Db\TableGateway\Feature\EventFeature;
use Laminas\Db\TableGateway\Feature;
use Laminas\Db\TableGateway\Feature\AbstractFeature;
use Laminas\Db\TableGateway\Feature\FeatureSet;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManager;
use Axleus\Db\Exception\InvalidArgumentException;

class TableGateway extends AbstractTableGateway
{
    /**
     *
     * @param string|TableIdentifier|array $table
     * @param AdapterInterface $adapter
     * @param null|AbstractFeature|FeatureSet $features
     * @param null|ResultSetInterface $resultSetInterface
     * @param null|Sql $sql
     * @param null|EventManager $eventManager
     * @param null|AbstractListenerAggregate $listener
     * @return void
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function __construct(
        string|TableIdentifier|array $table,
        AdapterInterface $adapter,
        Feature\AbstractFeature|Feature\FeatureSet|null $features = null,
        ?ResultSetInterface $resultSetInterface = null,
        ?Sql $sql = null,
        ?EventManager $eventManager = null,
        ?AbstractListenerAggregate $listener = null,
    ) {
        // Set the table name
        $this->table = $table;
        if ($adapter instanceof AdapterInterface) {
            $this->adapter = $adapter;
        }
        // process features
        if ($features !== null) {
            if ($features instanceof Feature\AbstractFeature) {
                $features = [$features];
            }
            if (is_array($features)) {
                $this->featureSet = new Feature\FeatureSet($features);
            } elseif ($features instanceof Feature\FeatureSet) {
                $this->featureSet = $features;
            } else {
                throw new Exception\InvalidArgumentException(
                    'TableGateway expects $feature to be an instance of an AbstractFeature or a FeatureSet, or an '
                    . 'array of AbstractFeatures'
                );
            }
        } else {
            $this->featureSet = new Feature\FeatureSet();
        }
        // attach this feature set to this instance
        $this->featureSet->setTableGateway($this);
        // Add the desired features, for us this is only the even feature currently
        // if we have an instance of the event manager and events are enabled, and we have a listener add this feature
        if ($eventManager instanceof EventManager
            && $listener instanceof AbstractListenerAggregate
        ) {
            $eventFeature = new EventFeature($eventManager);
            $eventManager = $eventFeature->getEventManager();
            $listener->attach($eventManager);
            $this->featureSet->addFeature($eventFeature);
        }
        $this->resultSetPrototype = $resultSetInterface ?? new ResultSet();

        // Sql object (factory for select, insert, update, delete)
        $this->sql = $sql ?: new Sql($this->adapter, $this->table);

        // check sql object bound to same table
        if ($this->sql->getTable() !== $this->table) {
            throw new Exception\InvalidArgumentException(
                'The table inside the provided Sql object must match the table of this TableGateway'
            );
        }
        // inititalize this instance
        $this->initialize();
    }

    /**
     * @param ResultSet $resultSetPrototype
     */
    public function setResultSetPrototype($resultSetPrototype): void
    {
        $this->resultSetPrototype = $resultSetPrototype;
    }
}
