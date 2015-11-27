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

namespace Plugin\Holiday\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

class HolidayServiceProvider implements ServiceProviderInterface
{

    /**
     * ナビに新しい項目を追加します.
     * @param array $nav ナビの配列参照
     * @param array $addNavi 追加するナビ配列
     * @param array $ids 追加するナビのid配列
     */
    private static function addNavi(array &$nav, array $addNavi, array $ids = array())
    {
        $targetId = array_shift($ids);
        if (!$targetId) {
            // IDが無ければトップレベルの最後に追加
            $nav[] = $addNavi;
        }

        foreach ($nav as $key => $val) {
            if (strcmp($targetId, $val["id"]) == 0) {
                if (count($ids) > 0) {
                    return self::addNavi($nav[$key]['child'], $addNavi, $ids);
                }
                // 最後に追加
                $nav[$key]['child'][] = $addNavi;
                return true;
            }
        }

        return false;
    }

    public function register(BaseApplication $app)
    {
        // 定休日テーブル用リポジトリ
        $app['eccube.plugin.holiday.repository.holiday'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\Holiday\Entity\Holiday');
        });
        
        $basePath = '/' . $app["config"]["admin_route"];
        // 一覧
        $app->match($basePath . '/system/shop/holiday/', '\Plugin\Holiday\Controller\HolidayController::index')
            ->bind('admin_setting_shop_holiday');
        // 新規作成
        $app->match($basePath . '/system/shop/holiday/new', '\Plugin\Holiday\Controller\HolidayController::edit')
            ->bind('admin_setting_shop_holiday_new');
        // 編集
        $app->match($basePath . '/system/shop/holiday/{id}/edit', '\Plugin\Holiday\Controller\HolidayController::edit')
            ->assert('id', '\d+')
            ->bind('admin_setting_shop_holiday_edit');
        // 一覧：削除
        $app->delete($basePath . '/system/shop/holiday/{id}/delete', '\Plugin\Holiday\Controller\HolidayController::delete')
            ->assert('id', '\d+')
            ->bind('admin_setting_shop_holiday_delete');
        // 一覧：上
        $app->put($basePath . '/system/shop/holiday/{id}/up', '\Plugin\Holiday\Controller\HolidayController::up')
            ->assert('id', '\d+')
            ->bind('admin_setting_shop_holiday_up');
        // 一覧：下
        $app->put($basePath . '/system/shop/holiday/{id}/down', '\Plugin\Holiday\Controller\HolidayController::down')
            ->assert('id', '\d+')
            ->bind('admin_setting_shop_holiday_down');

        $app->match('/block/holiday_calendar_block', '\Plugin\Holiday\Controller\Block\HolidayController::index')->bind('block_holiday_calendar_block');

        // 型登録
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\Holiday\Form\Type\HolidayType($app);
            return $types;
        }));


        // メッセージ登録
        $app['translator'] = $app->share($app->extend('translator', function ($translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());

            $file = __DIR__ . '/../Resource/locale/message.' . $app['locale'] . '.yml';
            if (file_exists($file)) {
                $translator->addResource('yaml', $file, $app['locale']);
            }

            return $translator;
        }));

        // load config
        $conf = $app['config'];
        $app['config'] = $app->share(function () use ($conf) {
            $confarray = array();
            $path_file = __DIR__ . '/../Resource/config/path.yml';
            if (file_exists($path_file)) {
                $config_yml = Yaml::parse(file_get_contents($path_file));
                if (isset($config_yml)) {
                    $confarray = array_replace_recursive($confarray, $config_yml);
                }
            }

            return array_replace_recursive($conf, $confarray);
        });

        // メニュー登録
        $app['config'] = $app->share($app->extend('config', function ($config) {
            $addNavi['id'] = "holiday";
            $addNavi['name'] = "定休日管理";
            $addNavi['url'] = "admin_setting_shop_holiday";

            self::addNavi($config['nav'], $addNavi, array('setting', 'shop'));

            return $config;
        }));
    }

    public function boot(BaseApplication $app)
    {
    }
}
