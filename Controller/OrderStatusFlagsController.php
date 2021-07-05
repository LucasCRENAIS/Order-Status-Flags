<?php

namespace OrderStatusFlags\Controller;

use OrderStatusFlags\Event\OrderStatusFlagsEvents;
use OrderStatusFlags\Exception\OrderStatusFlagsException;
use OrderStatusFlags\Form\OrderStatusFlagsCreationForm;
use OrderStatusFlags\Form\OrderStatusFlagsModificationForm;
use OrderStatusFlags\Model\Flags;
use OrderStatusFlags\Model\FlagsQuery;
use OrderStatusFlags\Model\OrderStatusFlags;
use OrderStatusFlags\Model\OrderStatusFlagsQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Thelia\Controller\Admin\AdminController;
use Thelia\Core\Event\UpdatePositionEvent;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;
use Thelia\Model\OrderStatusQuery;
use Thelia\Tools\URL;

class OrderStatusFlagsController extends AdminController
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Load an existing object from the database.
     *
     * @return \OrderStatusFlags\Model\Flags
     */
    protected function getExistingObject(Request $request)
    {
        $flags = FlagsQuery::create()
            ->findOneById($request->attributes->get('flags_id'));

        return $flags;
    }

    /**
     * @param $positionChangeMode
     * @param $positionValue
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createUpdatePositionEvent(Request $request)
    {
        $mode = UpdatePositionEvent::POSITION_ABSOLUTE;
        if (null !== $request->query->get('mode')) {
            $mode = 'up' == $request->query->get('mode') ? UpdatePositionEvent::POSITION_UP : UpdatePositionEvent::POSITION_DOWN;
        }
        $event = new UpdatePositionEvent(
            $request->query->get('flags_id'),
            $mode,
            $request->query->get('position')
        );

        $this->eventDispatcher->dispatch($event, OrderStatusFlagsEvents::ORDER_STATUS_FLAGS_UPDATE_POSITION);

        return $this->generateRedirect(URL::getInstance()->absoluteUrl('/admin/configuration/order-status'));
    }

    protected function getEditionArguments(Request $request)
    {
        return [
            'id' => $request->get('flags_id', 0),
            'current_tab' => $request->get('current_tab', 'general'),
        ];
    }

    public function createFlag(Request $request, Flags $flags)
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], ['OrderStatusFlags'], AccessManager::UPDATE)) {
            return $response;
        }

        $form = $this->createForm(OrderStatusFlagsCreationForm::getName());

        $url = '/admin/configuration/order-status/';

        try {
            $vform = $this->validateForm($form);

            $flags
                ->setLocale($this->getSession()->getAdminEditionLang()->getLocale())
                ->setTitle($vform->get('title')->getData())
                ->setCode($vform->get('code')->getData())
                ->setColor($vform->get('color')->getData());

            if (!$flags->getId()) {
                $flags->setPosition(
                    FlagsQuery::create()->orderByPosition(Criteria::DESC)->findOne()->getPosition() + 1
                );
            }

            $flags->save();

            // Redirect to the success URL,
            if ('stay' !== $request->get('save_mode')) {
            }
        } catch (\Exception $e) {
            $this->setupFormErrorContext(
                Translator::getInstance()->trans('flag created'),
                $message = $e->getMessage(),
                $form,
                $e
            );
        }

        $lastId = FlagsQuery::create()->orderById(Criteria::DESC)->findOne()->getId();

        return $this->generateRedirect(URL::getInstance()->absoluteUrl('/admin/configuration/order-status/update-flags/'.$lastId));
    }

    public function editFlag(Request $request)
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], ['OrderStatusFlags'], AccessManager::UPDATE)) {
            return $response;
        }

        $flags = $this->getExistingObject($request);
        $flags->setLocale(
            $this->getSession()->getAdminEditionLang()->getLocale()
        );

        $form = $this->createForm(OrderStatusFlagsModificationForm::getName(),
            FormType::class,
            [
                'color' => $flags->getColor(),
                'title' => $flags->getTitle(),
                'code' => $flags->getCode(),
                'description' => $flags->getDescription(),
                'id' => $request->get('flags_id'),
                'chapo' => $flags->getChapo(),
                'locale' => $flags->getLocale(),
                'position' => $flags->getPosition(),
                'postscriptum' => $flags->getPostscriptum(),
            ]
        );

        $this->getParserContext()->addForm($form);

        return $this->render('order-status-flags-edit',
            $this->getEditionArguments($request)
        );
    }

    public function saveFlag(Request $request)
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], ['OrderStatusFlags'], AccessManager::UPDATE)) {
            return $response;
        }

        $url = '/admin/configuration/order-status';

        $successMsg = null;
        $errorMsg = null;

        $form = $this->createForm(OrderStatusFlagsModificationForm::getName());

        $vform = $this->validateForm($form);

        $flags = $this->getExistingObject($request);

        $flags
            ->setLocale($this->getSession()->getAdminEditionLang()->getLocale())
            ->setTitle($vform->get('title')->getData())
            ->setCode($vform->get('code')->getData())
            ->setColor($vform->get('color')->getData())
            ->setChapo($vform->get('chapo')->getData())
            ->setDescription($vform->get('description')->getData())
            ->setPostscriptum($vform->get('postscriptum')->getData());

        if (!$flags->getId()) {
            $flags->setPosition(
                    FlagsQuery::create()->orderByPosition(Criteria::DESC)->findOne()->getPosition() + 1
                );
        }

        $flags->save();

        $ids = OrderStatusQuery::create()->find()->getData();

        $orderStatusIds = [];

        foreach ($ids as $id) {
            $orderStatusIds[] = $id->getId();
        }
        if (null == $vform->get('associated_status')->getData()) {
            foreach ($orderStatusIds as $lineToDelete) {
                $lineToDelete =
                        OrderStatusFlagsQuery::create()
                            ->filterByFlagId($vform->get('id')->getData())
                            ->filterByOrderStatusId($lineToDelete);
                $lineToDelete->delete();
            }
        } else {
            $unCheckedOnes = array_diff($orderStatusIds, $vform->get('associated_status')->getData());
            $checkedOnes = $vform->get('associated_status')->getData();

            foreach ($checkedOnes as $lineToAdd) {
                $isInDb = OrderStatusFlagsQuery::create()
                            ->filterByFlagId($vform->get('id')->getData())
                            ->filterByOrderStatusId($lineToAdd)
                            ->find()
                            ->getData();

                foreach ($isInDb as $v) {
                    $v->setOrderStatusId($lineToAdd)
                            ->setFlagId($vform->get('id')->getData())
                            ->save();
                }

                if (empty($isInDb)) {
                    $orderStatusFlags = new OrderStatusFlags();
                    $orderStatusFlags
                            ->setOrderStatusId($lineToAdd)
                            ->setFlagId($vform->get('id')->getData())
                            ->save();
                }
            }

            foreach ($unCheckedOnes as $lineToDelete) {
                $lineToDelete =
                        OrderStatusFlagsQuery::create()
                            ->filterByFlagId($vform->get('id')->getData())
                            ->filterByOrderStatusId($lineToDelete);
                $lineToDelete->delete();
            }
        }

        $successMsg = 1;

        return $this->generateRedirect(URL::getInstance()->absoluteUrl($url, ['errorMsg' => $errorMsg, 'successMsg' => $successMsg]));
    }

    public function deleteFlag(Request $request)
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], ['OrderStatusFlags'], AccessManager::UPDATE)) {
            return $response;
        }

        $successMsg = null;
        $errorMsg = null;

        $flagsToDelete = FlagsQuery::create()
            ->findOneById($request->request->get('order_status_flags_id'));

        try {
            $isProtected = $flagsToDelete->getProtectedStatus();
            if ($isProtected) {
                throw new OrderStatusFlagsException();
            }
            $flagsToDelete->delete();
            $successMsg = 1;
        } catch (OrderStatusFlagsException $exception) {
            $errorMsg = 1;
        }

        return $this->generateRedirect(URL::getInstance()->absoluteUrl('admin/configuration/order-status', ['errorMsg' => $errorMsg, 'successMsg' => $successMsg]));
    }
}
