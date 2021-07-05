<?php

namespace OrderStatusFlags\Model;

use OrderStatusFlags\Model\Base\OrderStatusFlagsQuery as BaseOrderStatusFlagsQuery;
use Thelia\Model\OrderStatusQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'order_status_flags' table.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class OrderStatusFlagsQuery extends BaseOrderStatusFlagsQuery
{
    public static function getList()
    {
        $orderStatusFlagsList = [];
        $orderStatus = OrderStatusQuery::create()->find();
        foreach ($orderStatus  as $status) {
            $orderStatusFlagsList[$status->getCode()] = $status->getId();
        }

        return $orderStatusFlagsList;
    }
}
