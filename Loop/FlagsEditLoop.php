<?php


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

class FlagsEditLoop extends BaseI18nLoop implements PropelSearchLoopInterface
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
        /** @var Flags $flag */
        foreach ($loopResult->getResultDataCollection() as $flag) {
            $loopResultRow = new LoopResultRow($flag);

                $loopResultRow->set('LOCALE', $this->locale)
                ->set('ID', $flag->getId())
                ->set('CODE', $flag->getCode())
                ->set('COLOR', $flag->getColor())
                ->set('POSITION', $flag->getPosition())
                ->set('PROTECTED_STATUS', $flag->getProtectedStatus())
                ->set('CREATED_AT', $flag->getCreatedAt())
                ->set('UPDATED_AT', $flag->getUpdatedAt())
                ->set('TITLE', $flag->getVirtualColumn('i18n_TITLE'));

            $this->addOutputFields($loopResultRow, $flag);

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }


}
