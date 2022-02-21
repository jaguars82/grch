<?php

namespace app\controllers;

use app\components\traits\CustomRedirects;
use app\models\Bank;
use app\models\Flat;
use app\models\search\BankSearch;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\form\BankCalculationForm;
use yii\helpers\ArrayHelper;

/**
 * BankController implements the CRUD actions for Bank model.
 */
class BankController extends Controller
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
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'calculation'],
                        'roles' => ['@'],
                    ],
                ]
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'app\components\actions\IndexWithSearch',
                'searchModelClass' => BankSearch::classname(),
            ],
        ];
    }

    /**
     * Displays a single Bank model.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $tariffDataProvider = new ActiveDataProvider([
            'query' => $model->getTariffs()
        ]);

        return $this->render('view', [
            'model' => $model,
            'tariffDataProvider' => $tariffDataProvider,
        ]);
    }
    
    /**
     * Credit calculation page for Bank model.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionCalculation($id, $flatId)
    {
        $bank = $this->findModel($id);
        
        if (($flat = Flat::findOne($flatId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }
        
        if (is_null($flat->newbuildingComplex->getBanks()->where(['bank.id' => $bank->id])->one())) {
            throw new NotFoundHttpException('Объект недвижимости не аккредитован в данном банке');
        }
        
        $tariffs = $flat->newbuildingComplex->preparedBankTariffs;
        if(isset($tariffs[$bank->id])) {
            $tariffs = ArrayHelper::index($tariffs[$bank->id], 'tariff_id');
        } else {
            $this->redirectWithError(['flat/view', 'id' => $flat->id], "У банка '{$flat->name}' нет тарифов для '{$flat->newbuildingComplex->name}'");
        }

        foreach($tariffs as $key => $tariff) {
            $tariffs[$key]['initialFee'] = $bank->calculateInitialFee($tariff, is_null($flat->price_credit) ? $flat->cashPriceWithDiscount : $flat->creditPriceWithDiscount);
        }
        
        $model = new BankCalculationForm();
        $currentTariff = current($tariffs);
        $currentTariff['tariff_id'] = $currentTariff['tariff_id'];
        
        $model->fill($currentTariff);

        return $this->render('calculation', [
            'bank' => $bank,
            'flat' => $flat,
            'tariffs' => $tariffs,
            'model' => $model,
        ]);
    }

    /**
     * Finds the Bank model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return Bank the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bank::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }
}
