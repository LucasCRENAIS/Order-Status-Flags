<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OrderStatusFlags\Event;

use OrderStatusFlags\OrderStatusFlags;
use Thelia\Core\Event\ActionEvent;
use OrderStatusFlags\Model\Flags;


class OrderStatusFlagsEvents extends ActionEvent
{
    public const ORDER_STATUS_FLAGS_CREATE = 'action.createOrderStatusFlags';
    public const ORDER_STATUS_FLAGS_UPDATE = 'action.updateOrderStatusFlags';
    public const ORDER_STATUS_FLAGS_DELETE = 'action.deleteOrderStatusFlags';
    public const ORDER_STATUS_FLAGS_UPDATE_POSITION = 'action.updateOrderStatusFlagsUpdatePosition';
}
