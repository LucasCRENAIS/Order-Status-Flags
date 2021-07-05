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

namespace OrderStatusFlags\Action;

use OrderStatusFlags\Event\OrderStatusFlagsCreateEvent;
use OrderStatusFlags\Event\OrderStatusFlagsDeleteEvent;
use OrderStatusFlags\Event\OrderStatusFlagsEvents;
use OrderStatusFlags\Event\OrderStatusFlagsUpdateEvent;
use OrderStatusFlags\Model\Flags;
use OrderStatusFlags\Model\FlagsQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\UpdatePositionEvent;
use Thelia\Core\Translation\Translator;

class OrderStatusFlagsAction extends BaseAction implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            OrderStatusFlagsEvents::ORDER_STATUS_FLAGS_CREATE => ['create', 128],
            OrderStatusFlagsEvents::ORDER_STATUS_FLAGS_UPDATE => ['update', 128],
            OrderStatusFlagsEvents::ORDER_STATUS_FLAGS_DELETE => ['delete', 128],
            OrderStatusFlagsEvents::ORDER_STATUS_FLAGS_UPDATE_POSITION => ['updatePosition', 128],
        ];
    }

    public function create(OrderStatusFlagsCreateEvent $event): void
    {
        $this->createOrUpdate($event, new Flags());
    }

    public function update(OrderStatusFlagsUpdateEvent $event): void
    {
        $orderStatusFlags = $this->getOrderStatusFlags($event);
        $this->createOrUpdate($event, $orderStatusFlags);
    }

    /**
     * @throws \Exception
     */
    public function delete(OrderStatusFlagsDeleteEvent $event): void
    {
        $orderStatusFlags = $this->getOrderStatusFlags($event);

        if ($orderStatusFlags->getProtectedStatus()) {
            throw new \Exception(Translator::getInstance()->trans('This status is protected.').' '.Translator::getInstance()->trans('You can not delete it.'));
        }

        if (null !== FLagsQuery::create()->findOneById($orderStatusFlags->getId())) {
            throw new \Exception(Translator::getInstance()->trans('Some commands use this status.').' '.Translator::getInstance()->trans('You can not delete it.'));
        }

        $orderStatusFlags->delete();

        $event->setOrderStatusFlags($orderStatusFlags);
    }

    protected function createOrUpdate(OrderStatusFlagsEvent $event, Flags $flags): void
    {
        $flags
            ->setCode(!$flags->getProtectedStatus() ? $event->getCode() : $flags->getCode())
            ->setColor($event->getColor())
            // i18n
            ->setLocale($event->getLocale())
            ->setTitle($event->getTitle())
            ->setDescription($event->getDescription())
            ->setPostscriptum($event->getPostscriptum())
            ->setChapo($event->getChapo());

        if (null === $flags->getId()) {
            $flags->setPosition(
                FlagsQuery::create()->orderByPosition(Criteria::DESC)->findOne()->getPosition() + 1
            );
        }

        $flags->save();

        $event->setOrderStatusFlags($flags);
    }

    /**
     * Changes position, selecting absolute ou relative change.
     *
     * @param $eventName
     */
    public function updatePosition(UpdatePositionEvent $event, $eventName, EventDispatcherInterface $dispatcher): void
    {
        $this->genericUpdatePosition(FlagsQuery::create(), $event, $dispatcher);
    }

    /**
     * @return Flags
     */
    protected function getOrderStatusFlags(OrderStatusFlagsUpdateEvent $event)
    {
        if (null === $orderStatusFlags = FlagsQuery::create()->findOneById($event->getId())) {
            throw new \LogicException('Flag not found');
        }

        return $orderStatusFlags;
    }
}
