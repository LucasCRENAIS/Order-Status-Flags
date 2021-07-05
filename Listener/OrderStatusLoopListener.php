<?php

namespace OrderStatusFlags\Listener;

use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Loop\LoopExtendsArgDefinitionsEvent;
use Thelia\Core\Event\Loop\LoopExtendsBuildModelCriteriaEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Model\OrderStatusQuery;

class OrderStatusLoopListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_ARG_DEFINITIONS, 'order_status') => ['argDefinitions', 128],
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_BUILD_MODEL_CRITERIA, 'order_status') => ['buildModelCriteria', 128],
        ];
    }

    /**
     * Add a new parameter for loop lang
     * you can now call the loop with this argument.
     */
    public function argDefinitions(LoopExtendsArgDefinitionsEvent $event)
    {
        $argument = $event->getArgumentCollection();
        $argument->addArgument(Argument::createIntListTypeArgument('flag_id'));
    }

    /**
     * Change the query search of the loop lang.
     */
    public function buildModelCriteria(LoopExtendsBuildModelCriteriaEvent $event)
    {
        $flagId = $event->getLoop()->getArgumentCollection()->get('flag_id');
        if (null === $flagId->getValue()) {
            return;
        }
        /** @var OrderStatusQuery $query */
        $query = $event->getModelCriteria();
        $query->innerJoinOrderStatusFlags()
            ->filterById($flagId->getValue(), Criteria::IN)
        ->groupById();
    }
}
