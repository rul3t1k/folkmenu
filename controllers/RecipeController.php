<?php

namespace app\controllers;

use app\models\Category;
use app\models\Favorite;
use app\models\Recipe;
use Yii;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/** Просмотр и модерация рецептов (только для администраторов). */
class RecipeController extends Controller
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
        $status = (string)$this->request->get('status', '');
        $categoryId = (int)$this->request->get('category', 0);

        $query = Recipe::find()->with(['category', 'user']);
        if ($q !== '') {
            $query->andWhere(['like', 'title', $q]);
        }
        if (in_array($status, ['Черновик', 'Опубликовано'], true)) {
            $query->andWhere(['status' => $status]);
        }
        if ($categoryId > 0) {
            $query->andWhere(['category_id' => $categoryId]);
        }

        $pages = new Pagination(['totalCount' => (clone $query)->count(), 'pageSize' => 15, 'forcePageParam' => false]);
        $recipes = $query->orderBy(['id' => SORT_DESC])->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('index', [
            'recipes' => $recipes, 'pages' => $pages, 'q' => $q, 'status' => $status, 'categoryId' => $categoryId,
            'categories' => Category::find()->orderBy('name')->all(),
        ]);
    }

    public function actionView($id)
    {
        $model = Recipe::find()
            ->with(['category', 'user', 'recipeIngredients.ingredient', 'recipeSteps'])
            ->where(['id' => (int)$id])->one();
        if ($model === null) {
            throw new NotFoundHttpException('Рецепт не найден.');
        }
        return $this->render('view', [
            'model' => $model,
            'favCount' => (int)Favorite::find()->where(['recipe_id' => $model->id])->count(),
        ]);
    }

    /** Основные поля и статус публикации. */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            $post = $this->request->post('Recipe', []);
            $model->setAttributes([
                'title' => trim((string)($post['title'] ?? $model->title)),
                'description' => $post['description'] ?? $model->description,
                'category_id' => $post['category_id'] ?? $model->category_id,
                'difficulty' => $post['difficulty'] ?? $model->difficulty,
                'status' => $post['status'] ?? $model->status,
            ]);
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Рецепт сохранён.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'categories' => Category::find()->orderBy('name')->all(),
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->deleteCascade()) {
            Yii::$app->session->setFlash('success', 'Рецепт удалён.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось удалить рецепт.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Recipe::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Рецепт не найден.');
    }
}
