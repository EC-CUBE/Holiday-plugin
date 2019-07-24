<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
* https://www.ec-cube.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\Holiday\Controller;

use Plugin\Holiday\Form\Type\HolidayType;
use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;

class HolidayController extends AbstractController
{
    private $main_title;
    private $sub_title;

    public function __construct()
    {
    }

    public function index(Application $app, Request $request)
    {
        $Holidays = $app['eccube.plugin.holiday.repository.holiday']
            ->findBy(array(), array('rank' => 'DESC'));
        $TargetHoliday = new \Plugin\Holiday\Entity\Holiday();

        $form = $app['form.factory']
            ->createBuilder('admin_holiday', $TargetHoliday)
            ->getForm();

        return $app->render('Holiday/Resource/template/admin//holiday.twig', array(
            'form' => $form->createView(),
            'holidays' => $Holidays,
        ));
    }

    public function edit(Application $app, Request $request, $id = null)
    {
        $repos = $app['eccube.plugin.holiday.repository.holiday'];
        if ($id) {
            $Holiday = $repos->find($id);
            if (!$Holiday) {
                throw new NotFoundHttpException();
            }
        } else {
            $Holiday = new \Plugin\Holiday\Entity\Holiday();
        }

        $form = $app['form.factory']
            ->createBuilder('admin_holiday', $Holiday)
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                // 日付の妥当性チェック
                // 閏年への対応.
                if ($form->get('month')->getData() == 2 && $form->get('day')->getData() == 29) {
                    $valid_date = true;
                } else {
                    $valid_date = checkdate($form->get('month')->getData(), $form->get('day')->getData(), date('Y'));
                }
                if (!$valid_date) {
                    $app->addError('admin.holiday.invalid_day.error', 'admin');
                } else {
                    $status = $repos->save($Holiday);

                    if ($status) {
                        $app->addSuccess('admin.holiday.save.complete', 'admin');

                        return $app->redirect($app->url('admin_setting_shop_holiday'));
                    } else {
                        $app->addError('admin.holiday.save.error', 'admin');
                    }
                }
            }
        }

        return $app->render('Holiday/Resource/template/admin//holiday_edit.twig', array(
            'form' => $form->createView(),
            'holiday' => $Holiday,
        ));

    }

    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $repos = $app['eccube.plugin.holiday.repository.holiday'];

        $TargetHoliday = $repos->find($id);
        
        if (!$TargetHoliday) {
            throw new NotFoundHttpException();
        }

        $status = $repos->delete($TargetHoliday);

        if ($status === true) {
            $app->addSuccess('admin.holiday.delete.complete', 'admin');
        } else {
            $app->addError('admin.holiday.delete.error', 'admin');
        }

        return $app->redirect($app->url('admin_setting_shop_holiday'));
    }

    public function up(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $repos = $app['eccube.plugin.holiday.repository.holiday'];

        $TargetHoliday = $repos->find($id);
        if (!$TargetHoliday) {
            throw new NotFoundHttpException();
        }

        $status = $repos->up($TargetHoliday);

        if ($status === true) {
            $app->addSuccess('admin.holiday.up.complete', 'admin');
        } else {
            $app->addError('admin.holiday.up.error', 'admin');
        }

        return $app->redirect($app->url('admin_setting_shop_holiday'));
    }

    public function down(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $repos = $app['eccube.plugin.holiday.repository.holiday'];

        $TargetHoliday = $repos->find($id);
        if (!$TargetHoliday) {
            throw new NotFoundHttpException();
        }

        $status = $repos->down($TargetHoliday);

        if ($status === true) {
            $app->addSuccess('admin.holiday.down.complete', 'admin');
        } else {
            $app->addError('admin.holiday.down.error', 'admin');
        }

        return $app->redirect($app->url('admin_setting_shop_holiday'));
    }

}
