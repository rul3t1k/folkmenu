<?php

namespace app\controllers;

use app\models\Favorite;
use app\models\Recipe;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Главная страница админ-панели. Доступна только администраторам.
 */
class AdminController extends Controller
{
    public function behaviors()
    {
        return ['access' => self::accessFilter()];
    }

    /**
     * Фильтр доступа «только администратор». Его же используют контроллеры user, recipe и favorite.
     * @param array $openRules дополнительные правила, разрешённые до проверки роли (например, регистрация)
     */
    public static function accessFilter(array $openRules = [])
    {
        return [
            'class' => AccessControl::class,
            'rules' => array_merge($openRules, [
                [
                    'allow' => true,
                    'matchCallback' => function () {
                        return !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin;
                    },
                ],
            ]),
            'denyCallback' => function () {
                return Yii::$app->user->isGuest
                    ? Yii::$app->user->loginRequired()
                    : Yii::$app->response->redirect(['site/index']);
            },
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'counts' => [
                'users' => (int)User::find()->count(),
                'recipes' => (int)Recipe::find()->count(),
                'drafts' => (int)Recipe::find()->where(['status' => 'Черновик'])->count(),
                'favorites' => (int)Favorite::find()->count(),
            ],
        ]);
    }
}
