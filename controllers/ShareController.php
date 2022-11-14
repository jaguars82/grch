<?php

namespace app\controllers;

use app\models\Commercial;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;
use yii\helpers\ArrayHelper;
use app\components\traits\CustomRedirects;
use app\components\flat\Layout;

class ShareController extends Controller
{
    use CustomRedirects;

    public function behaviors()
    {
        return [ 
                'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'commercial' => ['GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['commercial'],
                        'roles' => ['?', '@'],
                    ],
                ]
            ]
        ];
    }

    /**
     * @param type $id
     */
    public function actionCommercial($id)
    {
        $commercial = (new Commercial())->find()
            ->where(['id' => $id])
            ->one();

        $commercialArray = ArrayHelper::toArray($commercial);
        $commercialArray['initiator'] = ArrayHelper::toArray($commercial->initiator);
        $commercialArray['initiator']['organization'] = ArrayHelper::toArray($commercial->initiator->agency);

        $flats = $commercial->flats;

        $flatsArray = array();
        foreach ($flats as $flat) {

            /** creating floor layout with selected flat as svg file */
            $selectionLayout = (new Layout())->createFloorLayoutWithSelectedFlat($flat);

            $flatItem = ArrayHelper::toArray($flat);
            $flatItem['floorLayoutImage'] = !is_null($flat->floorLayout) ? $flat->floorLayout->image : NULL;
            $flatItem['developer'] = ArrayHelper::toArray($flat->developer);
            $flatItem['newbuildingComplex'] = ArrayHelper::toArray($flat->newbuilding->newbuildingComplex);
            $flatItem['newbuildingComplex']['address'] = $flat->newbuilding->newbuildingComplex->address;
            foreach ($flat->furnishes as $key => $furnish) {
                $finishing = ArrayHelper::toArray($furnish);
                $finishing['furnishImages'] = $furnish->furnishImages;
                $flatItem['finishing'][] = ArrayHelper::toArray($finishing);
            } 
            $flatItem['newbuilding'] = ArrayHelper::toArray($flat->newbuilding);
            $flatItem['entrance'] = ArrayHelper::toArray($flat->entrance);
            $flatItem['advantages'] = ArrayHelper::toArray($flat->newbuilding->newbuildingComplex->advantages);
            array_push($flatsArray, $flatItem);
        }

        $commercialMode = count($flatsArray) > 1 ? 'multiple' : 'single';

        return $this->inertia('Share/Commercial', [
            'commercial' => $commercialArray,
            'commercialMode'=>  $commercialMode,
            'flats' => $flatsArray,
        ]);
    }
}