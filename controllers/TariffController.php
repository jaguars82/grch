<?php

namespace app\controllers;

use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\Application;
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
            $developerInStatistics = false;
            if (is_array($model['developers_in_statistics'])) {
                $developerInStatistics = in_array($developer['id'], $model['developers_in_statistics']);
            }
            $developerRow['in_statistics'] = $developerInStatistics;
            $developerMonthDeals = 0;
            $developerRow['complexes'] = [];
            
            foreach ($developer->newbuildingComplexes as $complex) {
                if (array_key_exists($complex->id, $model['tariff_table'])) {
                    $complexRow = ArrayHelper::toArray($complex);
                    $complexRow['tariffs'] = $model['tariff_table'][$complex->id]['tariffs'];
                    $complexRow['termsOfPayment'] = $model['tariff_table'][$complex->id]['termsOfPayment'];

                    $complexMonthDeals = 0;

                    // count deals (ifthe developer isin statistics)
                    if ($developerInStatistics) {
                        $applications = Application::find()
                            ->where(['developer_id' => $developer->id])
                            ->andWhere([
                                'or',
                                [
                                    'and',
                                    ['=', 'DATE_FORMAT(ddu_cash_paydate, "%Y-%m")', date('Y-m')],
                                ],
                                [
                                    'and',
                                    ['=', 'DATE_FORMAT(ddu_mortgage_paydate, "%Y-%m")', date('Y-m')],
                                ],
                                [
                                    'and',
                                    ['=', 'DATE_FORMAT(ddu_matcap_paydate, "%Y-%m")', date('Y-m')],
                                ]
                                /*[
                                    'and',
                                    'MONTH(ddu_cash_paydate) = MONTH(NOW())',
                                    'YEAR(ddu_cash_paydate) = YEAR(NOW())'
                                ],
                                [
                                    'and',
                                    'MONTH(ddu_mortgage_paydate) = MONTH(NOW())',
                                    'YEAR(ddu_mortgage_paydate) = YEAR(NOW())'
                                ],
                                [
                                    'and',
                                    'MONTH(ddu_matcap_paydate) = MONTH(NOW())',
                                    'YEAR(ddu_matcap_paydate) = YEAR(NOW())'
                                ]*/
                            ])
                            /*->andWhere("MONTH(`ddu_cash_paydate`) = MONTH(NOW()) AND YEAR(`ddu_cash_paydate`) = YEAR(NOW())")
                            ->orWhere("MONTH(`ddu_mortgage_paydate`) = MONTH(NOW()) AND YEAR(`ddu_mortgage_paydate`) = YEAR(NOW())")
                            ->orWhere("MONTH(`ddu_matcap_paydate`) = MONTH(NOW()) AND YEAR(`ddu_matcap_paydate`) = YEAR(NOW())")*/
                            ->all();

                        foreach ($applications as $application) {
                            $appPayDates = [$application->ddu_cash_paydate, $application->ddu_mortgage_paydate, $application->ddu_matcap_paydate];
                            usort($appPayDates, function($a, $b) {
                                $dateTimestamp1 = strtotime($a);
                                $dateTimestamp2 = strtotime($b);
                            
                                return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
                            });

                            $maxPayDate = $appPayDates[count($appPayDates) - 1];

                            if (date('m', strtotime($maxPayDate)) == date('m')) {
                                $complexMonthDeals++;
                            }
                        }
                    }

                    $complexRow['complexMonthDeals'] = $complexMonthDeals;

                    $developerRow['complexes'][] = $complexRow;

                    $developerMonthDeals += $complexMonthDeals;
                }
            }

            $developerRow['developerMonthDeals'] = $developerMonthDeals;

            array_push($developersArr, $developerRow);
        }

        return $this->inertia('Tariff/Index', [
            'model' => $model,
            'developers' => $developersArr
        ]);
    }

}
