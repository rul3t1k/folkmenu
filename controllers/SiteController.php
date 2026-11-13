<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ProfileForm;
use app\models\ShoppingListItem;
use app\models\Recipe;
use app\models\RecipeForm;
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
                'only' => ['logout', 'profile', 'toggle-shopping-item', 'save-recipe', 'delete-recipe', 'toggle-favorite', 'add-to-shopping-list', 'shopping-list', 'update-shopping-item', 'delete-shopping-item', 'clear-bought-items', 'add-shopping-item', 'login'],
                'rules' => [
                    [
                        'actions' => ['logout', 'profile', 'toggle-shopping-item', 'save-recipe', 'delete-recipe', 'toggle-favorite', 'add-to-shopping-list', 'shopping-list', 'update-shopping-item', 'delete-shopping-item', 'clear-bought-items', 'add-shopping-item'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        // вход — только для гостей
                        'actions' => ['login'],
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
                    'toggle-shopping-item' => ['post'],
                    'save-recipe' => ['post'],
                    'delete-recipe' => ['post'],
                    'toggle-favorite' => ['post'],
                    'add-to-shopping-list' => ['post'],
                    'update-shopping-item' => ['post'],
                    'delete-shopping-item' => ['post'],
                    'clear-bought-items' => ['post'],
                    'add-shopping-item' => ['post'],
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
    /** ID рецептов, показываемых на главной. */
    const HOME_HERO_ID = 3;
    const HOME_POPULAR_IDS = [4, 5, 6];

    public function actionIndex()
    {
        $uid = Yii::$app->user->id;
        $with = ['category', 'user', 'recipeIngredients.ingredient'];

        $hero = Recipe::find()->with($with)
            ->where(['id' => self::HOME_HERO_ID, 'status' => 'Опубликовано'])->one();

        $popular = Recipe::find()->with($with)
            ->where(['id' => self::HOME_POPULAR_IDS, 'status' => 'Опубликовано'])->all();
        usort($popular, function ($a, $b) {
            return array_search((int)$a->id, self::HOME_POPULAR_IDS) <=> array_search((int)$b->id, self::HOME_POPULAR_IDS);
        });

        $categories = \app\models\Category::find()->orderBy(['id' => SORT_ASC])->limit(6)->all();
        $catCounts = [];
        foreach (Recipe::find()->select(['category_id', 'cnt' => 'COUNT(*)'])->where(['status' => 'Опубликовано'])
                     ->groupBy('category_id')->asArray()->all() as $row) {
            $catCounts[(int)$row['category_id']] = (int)$row['cnt'];
        }

        $myFavorites = $uid
            ? array_map('intval', \app\models\Favorite::find()->select('recipe_id')->where(['user_id' => $uid])->column())
            : [];

        return $this->render('index', [
            'hero' => $hero,
            'popular' => $popular,
            'categories' => $categories,
            'catCounts' => $catCounts,
            'myFavorites' => $myFavorites,
        ]);
    }

    /** Поиск рецептов по имеющимся продуктам: кладовая — первые 10 ингредиентов из БД, рецепты — из БД. */
    public function actionIngredientsSearch()
    {
        $pantry = \app\models\Ingredient::find()->orderBy(['id' => SORT_ASC])->limit(10)->all();
        $allNames = \app\models\Ingredient::find()->select('name')->orderBy('name')->column();

        $recipes = Recipe::find()
            ->with(['recipeIngredients' => function ($q) { $q->orderBy(['position' => SORT_ASC]); }, 'recipeIngredients.ingredient'])
            ->where(['status' => 'Опубликовано'])
            ->all();

        $photoUrl = function ($photo) {
            if (!$photo) {
                return null;
            }
            if (preg_match('~^(https?:)?//|^/~', $photo)) {
                return $photo;
            }
            $path = (strpos($photo, '/') === false ? 'images/' : '') . $photo;
            return Yii::getAlias('@web') . '/' . implode('/', array_map('rawurlencode', explode('/', $path)));
        };

        $recipesData = [];
        foreach ($recipes as $r) {
            $ings = [];
            foreach ($r->recipeIngredients as $ri) {
                if (!$ri->ingredient) {
                    continue;
                }
                $ings[] = [
                    'id' => (int)$ri->ingredient_id,
                    'name' => $ri->ingredient->name,
                    // «соль — по вкусу» и подобное не считаем обязательным
                    'optional' => $ri->quantity === null && mb_stripos((string)$ri->unit, 'вкус') !== false,
                ];
            }
            if (!$ings) {
                continue;
            }
            $recipesData[] = [
                'id' => (int)$r->id,
                'title' => $r->title,
                'description' => (string)$r->description,
                'time' => (int)$r->prep_time_min + (int)$r->cook_time_min,
                'servings' => (int)($r->servings ?: 4),
                'calories' => $r->calories === null ? null : (int)$r->calories,
                'photo' => $photoUrl($r->photo),
                'url' => \yii\helpers\Url::to(['site/recipe', 'id' => $r->id]),
                'ingredients' => $ings,
            ];
        }

        return $this->render('ingredients-search', [
            'pantry' => $pantry,
            'allNames' => $allNames,
            'recipesData' => $recipesData,
        ]);
    }

    public function actionProfile()
    {
        /** @var \app\models\User $user */
        $user = Yii::$app->user->identity;
        $profileForm = new ProfileForm($user);

        if ($profileForm->load(Yii::$app->request->post())) {
            if ($profileForm->save()) {
                Yii::$app->session->setFlash('success', 'Изменения профиля сохранены!');
                return $this->refresh();
            }
        }

        $recipes = $user->getRecipes()->with([
                'category',
                'recipeIngredients' => function ($q) { $q->orderBy(['position' => SORT_ASC, 'id' => SORT_ASC]); },
                'recipeIngredients.ingredient',
                'recipeSteps' => function ($q) { $q->orderBy(['step_number' => SORT_ASC]); },
            ])->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])->all();
        $favorites = \app\models\Recipe::find()
            ->innerJoin('favorite', 'favorite.recipe_id = recipe.id')
            ->where(['favorite.user_id' => $user->id])
            ->andWhere(['or', ['recipe.status' => 'Опубликовано'], ['recipe.user_id' => $user->id]])
            ->with('category')
            ->orderBy(['favorite.created_at' => SORT_DESC])
            ->all();
        $shoppingItems = $user->getShoppingListItems()
            ->with(['ingredient', 'recipe'])
            ->orderBy(['is_bought' => SORT_ASC, 'created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->all();
        $categories = \app\models\Category::find()->orderBy('name')->all();

        return $this->render('profile', [
            'user' => $user,
            'profileForm' => $profileForm,
            'recipes' => $recipes,
            'favorites' => $favorites,
            'shoppingItems' => $shoppingItems,
            'categories' => $categories,
            'ingredientNames' => \app\models\Ingredient::find()->select('name')->orderBy('name')->column(),
        ]);
    }

    /**
     * Создание (без id) или редактирование (с id) рецепта текущего пользователя.
     * Вызывается через AJAX из окна «Передать рецепт в архив».
     */
    public function actionSaveRecipe($id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($id) {
            $recipe = Recipe::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
            if ($recipe === null) {
                throw new \yii\web\NotFoundHttpException('Рецепт не найден');
            }
        } else {
            $recipe = new Recipe();
        }

        $form = new RecipeForm($recipe, Yii::$app->user->id);
        $form->load(Yii::$app->request->post(), '');
        if ($form->save()) {
            Yii::$app->session->setFlash('success', $id ? 'Рецепт обновлён!' : 'Рецепт записан в вашу кулинарную книгу!');
            return ['ok' => true];
        }
        Yii::$app->response->statusCode = 422;
        return ['ok' => false, 'errors' => $form->getErrorMessages()];
    }

    /** Отметить позицию списка покупок купленной / некупленной (AJAX). */
    public function actionToggleShoppingItem($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $item = ShoppingListItem::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if ($item === null) {
            throw new \yii\web\NotFoundHttpException('Позиция не найдена');
        }
        $item->is_bought = Yii::$app->request->post('bought') ? 1 : 0;
        $item->save(false, ['is_bought']);
        return ['ok' => true, 'bought' => (int)$item->is_bought];
    }

    /** Страница рецепта: данные из БД. Черновики видит только автор. */
    public function actionRecipe($id = null)
    {
        $recipe = $id ? Recipe::find()
            ->with([
                'category',
                'user',
                'recipeIngredients' => function ($q) { $q->orderBy(['position' => SORT_ASC, 'id' => SORT_ASC]); },
                'recipeIngredients.ingredient',
                'recipeSteps' => function ($q) { $q->orderBy(['step_number' => SORT_ASC]); },
            ])
            ->where(['id' => (int)$id])->one() : null;

        $isOwner = $recipe && !Yii::$app->user->isGuest && (int)$recipe->user_id === (int)Yii::$app->user->id;
        if ($recipe === null || ($recipe->status !== 'Опубликовано' && !$isOwner)) {
            throw new \yii\web\NotFoundHttpException('Рецепт не найден.');
        }

        $isFavorite = !Yii::$app->user->isGuest && \app\models\Favorite::find()
            ->where(['user_id' => Yii::$app->user->id, 'recipe_id' => $recipe->id])->exists();

        return $this->render('recipe', [
            'recipe' => $recipe,
            'isOwner' => $isOwner,
            'isFavorite' => $isFavorite,
        ]);
    }

    /** Удаление своего рецепта вместе со связанными записями. */
    public function actionDeleteRecipe($id)
    {
        $recipe = Recipe::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if ($recipe === null) {
            throw new \yii\web\NotFoundHttpException('Рецепт не найден');
        }
        $photo = $recipe->photo;
        $stepPhotos = \app\models\RecipeStep::find()->select('photo')->where(['recipe_id' => $recipe->id])->column();

        $tx = Yii::$app->db->beginTransaction();
        try {
            \app\models\RecipeIngredient::deleteAll(['recipe_id' => $recipe->id]);
            \app\models\RecipeStep::deleteAll(['recipe_id' => $recipe->id]);
            \app\models\Favorite::deleteAll(['recipe_id' => $recipe->id]);
            // позиции списков покупок сохраняем, просто отвязываем от рецепта
            ShoppingListItem::updateAll(['recipe_id' => null], ['recipe_id' => $recipe->id]);
            $recipe->delete();
            $tx->commit();
        } catch (\Throwable $e) {
            $tx->rollBack();
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->session->setFlash('error', 'Не удалось удалить рецепт.');
            return $this->redirect(['site/profile']);
        }

        if ($photo && strpos($photo, RecipeForm::UPLOAD_DIR . '/') === 0) {
            @unlink(Yii::getAlias('@webroot') . '/' . $photo);
        }
        foreach ($stepPhotos as $sp) {
            if ($sp && strpos($sp, RecipeForm::STEP_UPLOAD_DIR . '/') === 0) {
                @unlink(Yii::getAlias('@webroot') . '/' . $sp);
            }
        }
        Yii::$app->session->setFlash('success', 'Рецепт удалён.');
        return $this->redirect(['site/profile']);
    }

    /** Добавить / убрать рецепт из избранного (AJAX). */
    public function actionToggleFavorite($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $recipe = Recipe::findOne((int)$id);
        $uid = Yii::$app->user->id;
        if ($recipe === null || ($recipe->status !== 'Опубликовано' && (int)$recipe->user_id !== (int)$uid)) {
            throw new \yii\web\NotFoundHttpException('Рецепт не найден');
        }
        $fav = \app\models\Favorite::findOne(['user_id' => $uid, 'recipe_id' => $recipe->id]);
        if ($fav) {
            \app\models\Favorite::deleteAll(['user_id' => $uid, 'recipe_id' => $recipe->id]);
            return ['ok' => true, 'favorite' => false];
        }
        $fav = new \app\models\Favorite(['user_id' => $uid, 'recipe_id' => $recipe->id, 'created_at' => date('Y-m-d H:i:s')]);
        $fav->save(false);
        return ['ok' => true, 'favorite' => true];
    }

    /**
     * Добавить выбранные ингредиенты рецепта в список покупок (AJAX).
     * Количества пересчитываются под выбранное число порций (базовое — recipe.servings).
     */
    public function actionAddToShoppingList($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $recipe = Recipe::find()->with('recipeIngredients')->where(['id' => (int)$id])->one();
        $uid = Yii::$app->user->id;
        if ($recipe === null || ($recipe->status !== 'Опубликовано' && (int)$recipe->user_id !== (int)$uid)) {
            throw new \yii\web\NotFoundHttpException('Рецепт не найден');
        }

        $wanted = array_map('intval', (array)Yii::$app->request->post('ingredient_ids', []));
        $servings = max(1, min(100, (int)Yii::$app->request->post('servings', $recipe->servings ?: 4)));
        $ratio = $servings / max(1, (int)$recipe->servings);
        $added = 0;

        foreach ($recipe->recipeIngredients as $ri) {
            if (!in_array((int)$ri->ingredient_id, $wanted, true)) {
                continue;
            }
            $qty = $ri->quantity === null ? null : round($ri->quantity * $ratio, 2);
            $item = ShoppingListItem::findOne([
                'user_id' => $uid, 'ingredient_id' => $ri->ingredient_id, 'recipe_id' => $recipe->id, 'is_bought' => 0,
            ]);
            if ($item === null) {
                $item = new ShoppingListItem([
                    'user_id' => $uid, 'ingredient_id' => $ri->ingredient_id, 'recipe_id' => $recipe->id,
                    'is_bought' => 0, 'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
            $item->quantity = $qty;
            $item->unit = $ri->unit;
            $item->save(false);
            $added++;
        }
        return ['ok' => true, 'added' => $added];
    }

    /** Каталог: все опубликованные рецепты из БД (фильтры и сортировка работают на странице). */
    public function actionRecipes()
    {
        $recipes = Recipe::find()
            ->with('category')
            ->where(['status' => 'Опубликовано'])
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->all();

        $favCounts = [];
        foreach ((new \yii\db\Query())->select(['recipe_id', 'c' => 'COUNT(*)'])->from('favorite')->groupBy('recipe_id')->all() as $row) {
            $favCounts[(int)$row['recipe_id']] = (int)$row['c'];
        }
        $myFavorites = Yii::$app->user->isGuest ? [] : array_map('intval',
            \app\models\Favorite::find()->select('recipe_id')->where(['user_id' => Yii::$app->user->id])->column());

        return $this->render('recipes', [
            'recipes' => $recipes,
            'categories' => \app\models\Category::find()->orderBy('name')->all(),
            'favCounts' => $favCounts,
            'myFavorites' => $myFavorites,
        ]);
    }

    public function actionShoppingList()
    {
        $items = ShoppingListItem::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->with(['ingredient', 'recipe'])
            ->orderBy(['is_bought' => SORT_ASC, 'created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->all();

        return $this->render('shopping-list', ['items' => $items]);
    }

    private function findOwnShoppingItem($id)
    {
        $item = ShoppingListItem::findOne(['id' => (int)$id, 'user_id' => Yii::$app->user->id]);
        if ($item === null) {
            throw new \yii\web\NotFoundHttpException('Позиция не найдена');
        }
        return $item;
    }

    /** Изменить количество позиции (AJAX). */
    public function actionUpdateShoppingItem($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $item = $this->findOwnShoppingItem($id);
        $q = str_replace(',', '.', trim((string)Yii::$app->request->post('quantity')));
        if (!is_numeric($q) || (float)$q <= 0 || (float)$q > 99999999) {
            Yii::$app->response->statusCode = 422;
            return ['ok' => false, 'message' => 'Количество должно быть положительным числом'];
        }
        $item->quantity = round((float)$q, 2);
        $item->save(false, ['quantity']);
        return ['ok' => true, 'quantity' => (float)$item->quantity];
    }

    /** Удалить позицию (AJAX). */
    public function actionDeleteShoppingItem($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->findOwnShoppingItem($id)->delete();
        return ['ok' => true];
    }

    /** Убрать из списка все купленные позиции (AJAX). */
    public function actionClearBoughtItems()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $n = ShoppingListItem::deleteAll(['user_id' => Yii::$app->user->id, 'is_bought' => 1]);
        return ['ok' => true, 'removed' => (int)$n];
    }

    /** Добавить свой продукт в список (AJAX). */
    public function actionAddShoppingItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $name = trim(preg_replace('/\s+/u', ' ', (string)Yii::$app->request->post('name')));
        if ($name === '' || mb_strlen($name) > 255) {
            Yii::$app->response->statusCode = 422;
            return ['ok' => false, 'message' => 'Введите название продукта (до 255 символов)'];
        }
        $ing = \app\models\Ingredient::find()->where(['name' => $name])->one();
        if ($ing === null) {
            $ing = new \app\models\Ingredient(['name' => $name]);
            $ing->save(false);
        }
        $uid = Yii::$app->user->id;
        $exists = ShoppingListItem::find()->where([
            'user_id' => $uid, 'ingredient_id' => $ing->id, 'recipe_id' => null, 'is_bought' => 0,
        ])->exists();
        if (!$exists) {
            $item = new ShoppingListItem([
                'user_id' => $uid, 'ingredient_id' => $ing->id, 'recipe_id' => null,
                'is_bought' => 0, 'created_at' => date('Y-m-d H:i:s'),
            ]);
            $item->save(false);
        }
        return ['ok' => true, 'duplicate' => $exists];
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
