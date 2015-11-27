<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
* http://www.lockon.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\Holiday\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class HolidayType extends AbstractType
{
    private $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    /**
     * Build config type form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return type
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $months = range(1, 12);
        $days = range(1, 31);
        $builder
            ->add('title', 'text', array(
                'label' => 'タイトル',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array('message' => '※ タイトルが入力されていません。')),
                ),
            ))
            ->add('month', 'choice', array(
                'label' => '月',
                'choices' => array_combine($months, $months),
                'empty_value' => '',
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array('message' => '※ 月が入力されていません。')),
                ),
            ))
            ->add('day', 'choice', array(
                'label' => '日',
                'choices' =>  array_combine($days, $days),
                'empty_value' => '',
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(array('message' => '※ 日が入力されていません。')),
                ),
            ))
            ->add('id', 'hidden', array()
            )
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_holiday';
    }
}
