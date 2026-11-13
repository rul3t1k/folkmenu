<?php

namespace app\controllers;

use app\models\Favorite;
use Yii;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/** Просмотр избранного пользователей (только для администраторов). */
class FavoriteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => AdminController::accessFilter(),
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['delete' => ['POST']],
            ],
        ];
    }

    public function actionIndex()
    {
        $q = trim((string)$this->request->get('q', ''));

        $query = Favorite::find()->alias('f')->joinWith(['user u', 'recipe r'])->with(['user', 'recipe']);
        if ($q !== '') {
            $query->andWhere(['or',
                ['like', 'u.username', $q], ['like', 'u.fullname', $q], ['like', 'r.title', $q],
            ]);
        }

        $pages = new Pagination(['totalCount' => (clone $query)->count(), 'pageSize' => 15, 'forcePageParam' => false]);
        $items = $query->orderBy(['f.created_at' => SORT_DESC])->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('index', ['items' => $items, 'pages' => $pages, 'q' => $q]);
    }

    public function actionView($user_id, $recipe_id)
    {
        return $this->render('view', ['model' => $this->findModel($user_id, $recipe_id)]);
    }

    public function actionDelete($user_id, $recipe_id)
    {
        $this->findModel($user_id, $recipe_id)->delete();
        Yii::$app->session->setFlash('success', 'Запись удалена из избранного.');
        return $this->redirect(['index']);
    }

    protected function findModel($user_id, $recipe_id)
    {
        if (($model = Favorite::findOne(['user_id' => $user_id, 'recipe_id' => $recipe_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Запись не найдена.');
    }
}
