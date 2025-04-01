<?php

namespace app\controllers\admin;

use Yii;
use app\models\BankTariff;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\traits\CustomRedirects;
use app\models\Bank;
use app\models\form\BankTariffForm;
use app\components\SharedDataFilter;
use app\components\VisitBehavior;

/**
 * BankTariffController implements the CRUD actions for BankTariff model.
 */
class BankTariffController extends Controller
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
                    'get-for-banks' => ['POST']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'update', 'delete', 'get-for-banks'],
                        'roles' => ['admin'],
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ],
            'visit' => VisitBehavior::class,
        ];
    }

    /**
     * Lists all BankTariff models.
     * @return mixed
     */
    public function actionIndex($bankId)
    {
        if (($bank = Bank::findOne($bankId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }     
    
        $dataProvider = new ActiveDataProvider([
            'query' => BankTariff::find()
                ->where(['bank_id' => $bank->id]),
        ]);

        return $this->render('index', [
            'bank' => $bank,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new BankTariff model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($bankId)
    {
        if (($bank = Bank::findOne($bankId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        $form = new BankTariffForm();
        $form->bank_id = $bank->id; 

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post())) {         
            try {
                (new BankTariff())->fill($form->attributes)->save();
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }               

            return $this->redirectWithSuccess(['index', 'bankId' => $bank->id], 'Добавлен тариф');
        }

        return $this->render('create', [
            'bank' => $bank,
            'model' => $form,
        ]);
    }

    /**
     * Updates an existing BankTariff model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $form = (new BankTariffForm())->fill($model->attributes);
        $form->scenario = BankTariffForm::SCENARIO_UPDATE;

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {
            try {
                $model->fill($form->attributes);
                $model->save();
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }
            
            return $this->redirectWithSuccess(['index', 'bankId' => $model->bank->id], 'Тариф обновлен');
        }

        return $this->render('update', [
            'bank' => $model->bank,
            'bankTariff' => $model,
            'model' => $form
        ]);
    }

    /**
     * Deletes an existing BankTariff model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the BankTariff model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BankTariff the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BankTariff::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
