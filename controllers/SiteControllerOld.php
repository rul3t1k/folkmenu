<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\RegistrationForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'profile', 'login', 'registration'],
                'rules' => [
                    [
                        'actions' => ['logout', 'profile'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        // вход и регистрация — только для гостей
                        'actions' => ['login', 'registration'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
                // вошедшего пользователя уводим в профиль, гостя — на страницу входа
                'denyCallback' => function () {
                    return Yii::$app->user->isGuest
                        ? Yii::$app->user->loginRequired()
                        : $this->redirect(['site/profile']);
                },
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionIngredientsSearch()
    {
        return $this->render('ingredients-search');
    }

    public function actionProfile()
    {
        /** @var \app\models\User $user */
        $user = Yii::$app->user->identity;

        return $this->render('profile', [
            'user' => $user,
            'recipesCount' => (int)$user->getRecipes()->count(),
            'favoritesCount' => (int)$user->getFavorites()->count(),
            'shoppingCount' => (int)$user->getShoppingListItems()->andWhere(['is_bought' => 0])->count(),
        ]);
    }

    public function actionRecipe()
    {
        return $this->render('recipe');
    }

        public function actionRecipes()
    {
        return $this->render('recipes');
    }

    public function actionShoppingList()
    {
        return $this->render('shopping-list');
    }

    public function actionRegistration()
    {
        $model = new RegistrationForm();

        if ($model->load(Yii::$app->request->post())) {
            if (($user = $model->register()) !== null) {
                Yii::$app->user->login($user, 3600 * 24 * 30);
                Yii::$app->session->setFlash('success', 'Аккаунт успешно создан! Добро пожаловать в архив.');
                return $this->redirect(['site/profile']);
            }
        }

        $model->password = '';
        return $this->render('registration', ['model' => $model]);
    }
    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->session->setFlash('success', 'Вы успешно вошли в личный кабинет!');
            return $this->goBack(['site/profile']);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }


    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
