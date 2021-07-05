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

class OrderStatusFlagsUpdateEvent extends OrderStatusFLagsEvent
{
    /** @var int */
    protected $id;

    /**
     * OrderStatusUpdateEvent constructor.
     *
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return OrderStatusFlagsUpdateEvent
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
