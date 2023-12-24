<?php

namespace app\controllers;

use app\components\exceptions\AppException;
use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\Developer;
use app\models\Favorite;
use app\models\Flat;
use app\models\NewbuildingComplex;
use app\models\search\FavoriteFlatSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\base\InvalidParamException;
use tebe\inertia\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Region;
use app\models\City;
use app\models\District;
use yii\helpers\ArrayHelper;

/**
 * FavoriteController implements the CRUD actions for Favorite model.
 */
class FavoriteController extends Controller
{
    use CustomRedirects;
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'delete-flat' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'delete', 'delete-flat', 'delete-all-archived', 'archive', 'activate',
                                      'update-comment', 'delete-comment'],
                        'roles' => ['@'],
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ],
        ];
    }
    
    /**
     * Lists all Favorite models.
     * 
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FavoriteFlatSearch();

        $activeDataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        $activeItemsCount = $searchModel->itemsCount;
        $archiveDataProvider = $searchModel->search(\Yii::$app->request->queryParams, false);
        $archiveItemsCount = $searchModel->itemsCount;

        $cities = !is_null($searchModel->region_id) ? City::find()->forRegion($searchModel->region_id)->asArray()->all() : [];
        if(!empty($cities)) {
            $cities = ArrayHelper::map($cities, 'id', 'name');
        }
        $districts = !is_null($searchModel->city_id) ? District::find()->forCity($searchModel->city_id)->asArray()->all() : [];
        if(!empty($districts)) {
            $districts = ArrayHelper::map($districts, 'id', 'name');
        }

        return $this->inertia('Favorite/Index', [
            'searchModel' => $searchModel,
            'activeDataProvider' => ArrayHelper::toArray($activeDataProvider->getModels(), [
                'app\models\Favorite' => [
                    'id', 'user_id', 'flat_id', 'comment', 'archived_by', 
                    'flat' => function ($favorite) {
                        return ArrayHelper::toArray($favorite->flat, [
                            'app\models\Flat' => [
                                'id', 'newbuilding_id', 'entrance_id', 'address', 'detail', 'area', 'rooms', 'floor', 'index_on_floor', 'price_cash', 'status', 'sold_by_application', 'is_applicated', 'is_reserved', 'created_at', 'updated_at', 'unit_price_cash', 'discount_type', 'discount', 'discount_amount', 'discount_price', 'azimuth', 'notification', 'extra_data', 'composite_flat_id', 'section', 'number', 'layout', 'unit_price_credit', 'price_credit', 'floor_position', 'floor_layout', 'layout_coords', 'is_euro', 'is_studio',
                                'newbuilding' => function ($flat) {
                                    return ArrayHelper::toArray($flat->newbuilding);
                                },
                                'newbuildingComplex' => function ($flat) {
                                    return ArrayHelper::toArray($flat->newbuildingComplex);
                                },
                                'developer' => function ($flat) {
                                    return ArrayHelper::toArray($flat->developer);
                                }
                            ]
                        ]);
                    }
                ]
            ]),
            'archiveDataProvider' => ArrayHelper::toArray($archiveDataProvider->getModels()),
            'paginationActive' => [
                'page' => $activeDataProvider->getPagination()->getPage(),
                'totalPages' => $activeDataProvider->getPagination()->getPageCount()
            ],
            'paginationArchive' => [
                'page' => $archiveDataProvider->getPagination()->getPage(),
                'totalPages' => $archiveDataProvider->getPagination()->getPageCount()
            ],
            'activeItemsCount' => $activeItemsCount,
            'archiveItemsCount' => $archiveItemsCount,
            'developers' => Developer::getAllAsList(),
            'regions' => Region::getAllAsList(),
            'cities' => $cities,
            'districts' => $districts
        ]);
    }

    /**
     * Creates a new Favorite model.
     * 
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($flatId)
    {
        try {
            if (($flat = Flat::findOne($flatId)) === null) {
                throw new NotFoundHttpException('Данные отсутсвуют');            
            }

            if ($flat->isSold()) {
                throw new AppException('Проданную квартиру нельзя добавить в избранное');           
            }

            \Yii::$app->user->identity->link('favoriteFlats', $flat);
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;        
        \Yii::$app->response->data = ['status' => 'success', 'message' => 'Квартира добавлена в список избранного'];

        // return $this->redirectWithSuccess(['flat/view', 'id' => $flatId], 'Квартира добавлена в избранное');
    }

    /**
     * Deletes an existing Favorite model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirectWithSuccess(['index'], 'Квартира удалена из избранного');
    }
    
    /**
     * Deletes an existing Favorite model for given flat.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteFlat($flatId)
    {
        if (($flat = Flat::findOne($flatId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');            
        }
        
        if (($model = Favorite::find()->forCurrentUser()->andWhere(['flat_id' => $flat->id])->one()) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');            
        }
        
        $model->delete();

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;        
        \Yii::$app->response->data = ['status' => 'success', 'message' => 'Квартира удалена из списка избранного'];
        
        // return $this->redirectWithSuccess(['flat/view', 'id' => $flat->id], 'Квартира удалена из избранного');
    }
    
    /**
     * Deletes all archived favorites.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteAllArchived()
    {
        Favorite::deleteAll([
            'AND',
            'user_id' => \Yii::$app->user->id,
            'archived_by IS NOT NULL'
        ]);
        
        return $this->redirectWithSuccess(['index'], 'Все избранное в архиве удалено');
    }
    
    /**
     * Move comment for favorite flat to archive state.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionArchive($id)
    {
        $model = $this->findModel($id);
        $model->archive()->save();
        
        return $this->redirectWithSuccess(['index'], 'Избранное отправлено в архив');
    }
    
    /**
     * Move comment for favorite flat to active state.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionActivate($id)
    {
        $model = $this->findModel($id);
        $model->activate()->save();
        
        return $this->redirectWithSuccess(['index'], 'Избранное восстановлено из архива');
    }
    
    /**
     * Updates an comment of existing Favorite model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateComment($id, $type)
    {
        $model = $this->findModel($id);
        
        if (is_null($model->comment = \Yii::$app->request->post('comment')) || !in_array($type, ['create', 'update'])) {
            throw new InvalidParamException();   
        }
        
        if ($type == 'create' && empty($model->comment)) {
            return $this->redirect(['index']);
        }
        
        $model->comment = empty($model->comment) ? NULL : $model->comment;      
        $model->save();
        
        return $this->redirectWithSuccess(['index'], ($type == 'create') ? 'Комментарий добавлен' : 'Комментарий обновлен');
    }
    
    /**
     * Delete an comment of existing Favorite model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDeleteComment($id)
    {
        $model = $this->findModel($id);
        
        $model->comment = NULL;      
        $model->save();
                
        return $this->redirectWithSuccess(['index'], 'Комментарий удален');
    }

    /**
     * Finds the Favorite model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return Favorite the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Favorite::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');            
        }

        return $model;
    }
}
