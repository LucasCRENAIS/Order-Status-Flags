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

namespace OrderStatusFlags\Hook;

use OrderStatusFlags\Loop\OrderStatusFlagsLoop;
use OrderStatusFlags\Model\FlagsQuery;
use OrderStatusFlags\Model\OrderStatusFlagsQuery;
use OrderStatusFlags\OrderStatusFlags;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\ModuleConfig;
use Thelia\Model\ModuleConfigQuery;

class OrderStatusFlagsHook extends BaseHook
{
    public function orderStatusFlagsBottom(HookRenderEvent $event)
    {
        $event->add(
            $this->render(
                'order-status-flags-module.html'
            )
        );
    }

    public function orderStatusFlagsConfig(HookRenderEvent $event)
    {
        if (null !== $params = ModuleConfigQuery::create()->findByModuleId(OrderStatusFlags::getModuleId())) {
            /** @var ModuleConfig $param */
            foreach ($params as $param) {
                $vars[$param->getName()] = $param->getValue();
            }
        }
        $event->add($this->render('order-status-flags-configuration.html'));
    }

    public function orderStatusFlagsConfigJs(HookRenderEvent $event)
    {
        $event->add(
            $this->render(
            'order-status-flags-configuration-js.html'
        ));
    }
}
