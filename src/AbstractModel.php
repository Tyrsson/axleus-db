<?php

declare(strict_types=1);

namespace Axleus\Db;

use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\Filter\FilterPluginManager;
use Laminas\Stdlib\ArrayObject;

use DateTimeImmutable;
use DateTimeZone;

abstract class AbstractModel extends ArrayObject
{
    /** @var TableGatewayInterface $gateway */
    protected $gateway;
    /** @var int|string $timeStamp */
    protected $timeStamp;
    /** @var Select $select */
    protected $select;
    /** @var Where $where*/
    protected $where;
    /**
     * Prevent unknown column errors
     *
     * @var array<string, int|string|array> $columnMap */
    protected array $columnMap = [];

    public function __construct(
        ?TableGateway $gateway = null,
        array $data = [],
        protected array $config = []
    ) {
        parent::__construct($data, ArrayObject::ARRAY_AS_PROPS);
        if ($gateway !== null) {
            $this->gateway = $gateway;
        }
        $this->config = $config;
        $this->select = new Select();
        $this->where  = new Where();
    }

    public function getTimeStamp(bool $useConfigFormat = true): int|string
    {
        $format   = $this->config['db']['db_time_format'] ?? DateTimeImmutable::RFC3339;
        $dateTime = new DateTimeImmutable('now', new DateTimeZone($this->config['server']['time_zone']));
        return $this->timeStamp = $dateTime->format($format);
    }

    public function roundToGivenDigit($number, $digit): float
    {
        $multiplier = 1;
        while ($number < 0.1) {
            $number *= 10;
            $multiplier /= 10;
        }
        while ($number >= 1) {
            $number /= 10;
            $multiplier *= 10;
        }
        return round($number, $digit) * $multiplier;
    }

    public function toArray()
    {
        return $this->getArrayCopy();
    }
}
