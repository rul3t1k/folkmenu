<?php

namespace app\controllers;

use app\models\Favorite;
use app\models\RegistrationForm;
use app\models\ShoppingListItem;
use app\models\User;
use Yii;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Регистрация (create, для гостей) и управление пользователями (остальное — только для администраторов).
 */
class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => AdminController::accessFilter([
                ['actions' => ['create'], 'allow' => true, 'roles' => ['?']],
            ]),
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['delete' => ['POST']],
            ],
        ];
    }

    /** Список пользователей с поиском и пагинацией. */
    public function actionIndex()
    {
        $q = trim((string)$this->request->get('q', ''));
        $role = $this->request->get('role', '');

        $query = User::find();
        if ($q !== '') {
            $query->andWhere(['or',
                ['like', 'username', $q], ['like', 'mail', $q], ['like', 'fullname', $q], ['like', 'phone', $q],
            ]);
        }
        if ($role !== '' && in_array((string)$role, ['0', '1'], true)) {
            $query->andWhere(['role' => (int)$role]);
        }

        $pages = new Pagination(['totalCount' => (clone $query)->count(), 'pageSize' => 15, 'forcePageParam' => false]);
        $users = $query->orderBy(['id' => SORT_ASC])->offset($pages->offset)->limit($pages->limit)->all();

        $ids = array_map(function ($u) { return (int)$u->id; }, $users);
        $recipeCounts = $ids ? \app\models\Recipe::find()->select(['user_id', 'c' => 'COUNT(*)'])->where(['user_id' => $ids])->groupBy('user_id')->asArray()->all() : [];
        $recipeCounts = array_column($recipeCounts, 'c', 'user_id');

        return $this->render('index', [
            'users' => $users, 'pages' => $pages, 'q' => $q, 'role' => (string)$role, 'recipeCounts' => $recipeCounts,
        ]);
    }

    /** Карточка пользователя. */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
            'recipes' => $model->getRecipes()->orderBy(['id' => SORT_DESC])->all(),
            'favCount' => (int)Favorite::find()->where(['user_id' => $model->id])->count(),
        ]);
    }

    /** Регистрация нового пользователя. */
    public function actionCreate()
    {
        $model = new RegistrationForm();

        if ($model->load($this->request->post()) && ($user = $model->register()) !== null) {
            Yii::$app->user->login($user, 3600 * 24 * 30);
            Yii::$app->session->setFlash('success', 'Аккаунт успешно создан! Добро пожаловать в архив.');
            return $this->redirect(['site/profile']);
        }

        $model->password = '';
        return $this->render('create', ['model' => $model]);
    }

    /** Редактирование данных пользователя (в том числе роли). Пароль меняется, только если введён новый. */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $newPassword = '';

        if ($this->request->isPost) {
            $post = $this->request->post('User', []);
            $newPassword = (string)($post['newPassword'] ?? '');
            $model->setAttributes([
                'username' => $post['username'] ?? $model->username,
                'fullname' => $post['fullname'] ?? $model->fullname,
                'mail' => $post['mail'] ?? $model->mail,
                'phone' => User::normalizePhone($post['phone'] ?? ''),
                'bio' => $post['bio'] ?? $model->bio,
                'role' => $post['role'] ?? $model->role,
            ], false);

            // администратор не может сам снять с себя права
            if ((int)$model->id === (int)Yii::$app->user->id) {
                $model->role = User::ROLE_ADMIN;
            }
            if ($newPassword !== '') {
                if (mb_strlen($newPassword) < 6) {
                    $model->addError('password', 'Пароль должен содержать не менее 6 символов.');
                } else {
                    $model->password = $newPassword; // захэшируется в beforeSave
                }
            }
            if (!$model->hasErrors() && $model->save()) {
                Yii::$app->session->setFlash('success', 'Данные пользователя сохранены.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    /** Удаление пользователя. Пользователя с рецептами и самого себя удалить нельзя. */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ((int)$model->id === (int)Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'Нельзя удалить собственную учётную запись.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        if ($model->getRecipes()->count() > 0) {
            Yii::$app->session->setFlash('error', 'У пользователя есть рецепты — сначала удалите или переназначьте их.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        Favorite::deleteAll(['user_id' => $model->id]);
        ShoppingListItem::deleteAll(['user_id' => $model->id]);
        $avatar = $model->avatar;
        $model->delete();
        if ($avatar && strpos($avatar, 'uploads/avatars/') === 0) {
            @unlink(Yii::getAlias('@webroot') . '/' . $avatar);
        }

        Yii::$app->session->setFlash('success', 'Пользователь удалён.');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Пользователь не найден.');
    }
}
