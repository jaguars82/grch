<?php

namespace app\controllers\admin;

use app\components\exceptions\AppException;
use app\components\traits\CustomRedirects;
use app\models\Newbuilding;
use app\models\service\Entrance;
use app\models\form\EntranceForm;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class EntranceController extends \yii\web\Controller
{

    use CustomRedirects;

    public $layout = 'admin';
    
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
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'roles' => ['admin'],
                    ],
                ]
            ],
        ];
    }

    public function actionIndex($newbuildingId)
    {
        if (($newbuilding = Newbuilding::findOne($newbuildingId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Entrance::find()->where(['newbuilding_id' => $newbuilding->id]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'newbuilding' => $newbuilding,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCreate($newbuildingId)
    {
        if (($newbuilding = Newbuilding::findOne($newbuildingId)) === null) {
            throw new NotFoundHttpException('Позиция не найдена');
        }
        
        $form = new EntranceForm();
        $form->newbuilding_id = $newbuilding->id;

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {         
            try {
                Entrance::create($form->attributes);
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }
            
            return $this->redirectWithSuccess(['index', 'newbuildingId' => $newbuilding->id], 'Подъезд добавлен');
        }

        return $this->render('create', [
            'model' => $form,
            'newbuilding' => $newbuilding
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $form = (new EntranceForm())->fill($model->attributes);
        $form->scenario = EntranceForm::SCENARIO_UPDATE;

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {
            try {
                $model->edit($form->attributes);
            } catch (\Exception $e) {
                echo '<pre>'; var_dump($e); echo '</pre>'; die();
                return $this->redirectBackWhenException($e);
            }
            
            return $this->redirectWithSuccess(['index', 'newbuildingId' => $model->newbuilding->id], 'Подъезд обновлен');
        }

        return $this->render('update', [
            'model' => $form,
            'entrance' => $model,
        ]);
    }

    /**
     * Deletes an existing Entrance model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $newbuildingId = $model->newbuilding_id;
        
        try {
            if (count($model->flats)) {
                throw new AppException('Позиция не может быть удалена, так как имеет квартиры');
            }            
            $model->delete();
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }

        return $this->redirectWithSuccess(['index', 'newbuildingId' => $newbuildingId], 'Подъезд удален');
    }    

    /**
     * Finds the Newbuilding model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return Entrance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Entrance::findOne($id)) === null) {
            throw new NotFoundHttpException('Подъезд не найден');
        }

        return $model;
    }    

}
