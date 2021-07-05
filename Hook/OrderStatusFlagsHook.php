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

use OrderStatusFlags\OrderStatusFlags;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\ModuleConfig;
use Thelia\Model\ModuleConfigQuery;

class OrderStatusFlagsHook extends BaseHook
{
    public function orderStatusFlagsConfig(HookRenderEvent $event)
    {
        if (null !== $params = ModuleConfigQuery::create()->findByModuleId(OrderStatusFlags::getModuleId())) {
            /** @var ModuleConfig $param */
            foreach ($params as $param) {
                $vars[$param->getName()] = $param->getValue();
            }

            $event->add(
                $this->render(
                    'order-status-flags-configuration.html')
            );
        }
    }

    public function orderStatusFlagsConfigJs(HookRenderEvent $event)
    {
        $event->add(
            $this->render(
            'order-status-flags-configuration-js.html'
        ));
    }
}
