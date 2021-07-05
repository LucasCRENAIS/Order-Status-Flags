<?php

namespace OrderStatusFlags\Model;

use OrderStatusFlags\Model\Base\Flags as BaseFlags;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Thelia\Model\Tools\PositionManagementTrait;

/**
 * Skeleton subclass for representing a row from the 'flags' table.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class Flags extends BaseFlags
{
    use PositionManagementTrait;

    /**
     * Implementors may add some search criteria (e.g., parent id) to the queries
     * used to change/get position by overloading this method.
     *
     * @param $query FlagsQuery
     */
    protected function addCriteriaToPositionQuery(ModelCriteria $query): void
    {
//        $query->filterById($this->getId());
    }
}
