<?php

namespace app\controllers;

use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\Developer;
use app\models\Tariff;
use yii\data\ActiveDataProvider;
use tebe\inertia\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * TariffController implements the CRUD actions for Faq model.
 */
class TariffController extends Controller
{
    use CustomRedirects;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['admin', 'agent', 'manager'],
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ],
        ];
    }

    /**
     * Lists all Faq models.
     * @return mixed
     */
    public function actionIndex($id = null)
    {
        if (empty($id)) {
            $model = Tariff::find()->orderBy('id DESC')->one();
        } else {
            $model = Tariff::findOne($id);
        }

        $model = ArrayHelper::toArray($model);

        $developers = (new Developer())->find()->all();
        $developersArr = Array();

        foreach ($developers as $developer) {
            $developerRow = ArrayHelper::toArray($developer);
            $developerRow['complexes'] = [];
            foreach ($developer->newbuildingComplexes as $complex) {
                if (array_key_exists($complex->id, $model['tariff_table'])) {
                    $complexRow = ArrayHelper::toArray($complex);
                    $complexRow['tariffs'] = $model['tariff_table'][$complex->id]['tariffs'];
                    $complexRow['termsOfPayment'] = $model['tariff_table'][$complex->id]['termsOfPayment'];
                    $developerRow['complexes'][] = $complexRow;
                }
            }
            array_push($developersArr, $developerRow);
        }

        return $this->inertia('Tariff/Index', [
            'model' => $model,
            'developers' => $developersArr
        ]);
    }

}
