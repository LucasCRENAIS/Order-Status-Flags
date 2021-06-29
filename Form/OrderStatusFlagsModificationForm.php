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

namespace OrderStatusFlags\Form;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\GreaterThan;

class OrderStatusFlagsModificationForm extends OrderStatusFlagsCreationForm
{

    protected function buildForm(): void
    {
        $this->formBuilder->add('id', HiddenType::class, [
            'required' => true,
            'constraints' => [
                new GreaterThan(['value' => 0]),
            ],
        ]);

        parent::buildForm();

        $this->addStandardDescFields();
    }

    public static function getName()
    {
        return 'flags_modification';
    }
}
