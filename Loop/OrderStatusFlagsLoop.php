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

namespace OrderStatusFlags\Loop;

use OrderStatusFlags\Model\Flags;
use OrderStatusFlags\Model\FlagsQuery;
use OrderStatusFlags\Model\OrderStatusFlags;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * OrderStatus loop.
 *
 * Class OrderStatusLoop
 *
 * @author Etienne Roudeix <eroudeix@openstudio.fr>
 * @author Gilles Bourgeat <gbourgeat@gmail.com>
 *
 * @method int[]    getId()
 * @method string   getCode()
 * @method string[] getOrder()
 */
class OrderStatusFlagsLoop extends BaseI18nLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createAnyTypeArgument('code'),
            Argument::createEnumListTypeArgument(
                'order',
                [
                    'alpha',
                    'alpha_reverse',
                    'manual',
                    'manual_reverse',
                ],
                'manual'
            )
        );
    }

    public function buildModelCriteria()
    {
        $search = FlagsQuery::create();

        /* manage translations */
        $this->configureI18nProcessing($search);

        if (null !== $id = $this->getId()) {
            $search->filterById($id, Criteria::IN);
        }

        if (null !== $code = $this->getCode()) {
            $search->filterByCode($code, Criteria::EQUAL);
        }

        $orders = $this->getOrder();

        foreach ($orders as $order) {
            switch ($order) {
                case 'alpha':
                    $search->addAscendingOrderByColumn('i18n_TITLE');
                    break;
                case 'alpha_reverse':
                    $search->addDescendingOrderByColumn('i18n_TITLE');
                    break;
                case 'manual':
                    $search->orderByPosition(Criteria::ASC);
                    break;
                case 'manual_reverse':
                    $search->orderByPosition(Criteria::DESC);
                    break;
            }
        }

        return $search;
    }

    public function parseResults(LoopResult $loopResult)
    {
        /** @var Flags $flags */
            foreach ($loopResult->getResultDataCollection() as $flags) {
            $loopResultRow = new LoopResultRow($flags);
            $orderStatusIds = [];
            /** @var OrderStatusFlags $orderStatusFlag */
                foreach ($flags->getOrderStatusFlagss() as $orderStatusFlag)
            {
               $orderStatusIds[] = $orderStatusFlag->getOrderStatusId();

            }
                $loopResultRow->set('ID', $flags->getId())

                ->set('LOCALE', $this->locale)
                ->set('CODE', $flags->getCode())
                ->set('COLOR', $flags->getColor())
                ->set('POSITION', $flags->getPosition())
                ->set('PROTECTED_STATUS', $flags->getProtectedStatus())
                ->set('ORDER_STATUS_IDS', $orderStatusIds)
                ->set('TITLE', $flags->getVirtualColumn('i18n_TITLE'));

                $linkedStatus = [
                    $flags->getId() => $orderStatusIds
                ];

                $loopResultRow->set('LINKED_STATUS', $linkedStatus);

            $this->addOutputFields($loopResultRow, $flags);

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
