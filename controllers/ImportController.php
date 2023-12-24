<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

use app\components\exceptions\AppException;
use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\form\ImportForm;
use app\models\service\Developer;
use app\models\service\NewbuildingComplex;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Implements action for import flat's data
 */
class ImportController extends Controller
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
                'only' => ['import-for-developer', 'for-newbuilding-complex', 'check-import'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['for-developer', 'for-newbuilding-complex'],
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['check-import'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            [
                'class' => SharedDataFilter::class
            ],
        ];
    }
    
    /**
     * 
     * @param integer $developerId
     * @param boolean $useEndpoint
     * @return mixed
     * @throws NotFoundHttpException
     * @throws AppException
     */
    public function actionForDeveloper($developerId, $useEndpoint = false)
    {
        if (($model = Developer::findOne($developerId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }
        
        try {
            if ($useEndpoint && (is_null($model->import) || empty($model->import->endpoint))){
                throw new NotFoundHttpException('На заданы настройки импорта');
            }

            $form = new ImportForm();
            $form->importObject = $model->import;        
            $form->endpoint = $useEndpoint ? $model->import->endpoint : NULL;
        
            if ($useEndpoint) {
                if ($useEndpoint && !$form->process()) {
                    throw new AppException('Произошла ошибка. Обратитесь в службу поддержки');
                }
            } else {
                if (!\Yii::$app->request->isPost || !$form->load(\Yii::$app->request->post()) || !$form->process()) {
                    throw new AppException('Произошла ошибка. Обратитесь в службу поддержки');
                }
            }
            
            $model->import($form->data);
        } catch (\Exception $e) {            
            return $this->redirectBackWhenException($e);            
        }
        
        return $this->redirectWithSuccess(['developer/view', 'id' => $model->id], $this->getImportMessage($model));
    }
    
    /**
     * 
     * @param integer $newbuildingComplexId
     * @param boolean $useEndpoint
     * @return mixed
     * @throws NotFoundHttpException
     * @throws AppException
     */
    public function actionForNewbuildingComplex($newbuildingComplexId, $useEndpoint = false)
    {
        if (($model = NewbuildingComplex::findOne($newbuildingComplexId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }
        
        try {
            if ($useEndpoint && (is_null($model->import) || empty($model->import->endpoint))){
                throw new NotFoundHttpException('На заданы настройки импорта');
            }

            $form = new ImportForm();
            $form->importObject = $model->import;        
            $form->endpoint = $useEndpoint ? $model->import->endpoint : NULL;
        
            if ($useEndpoint) {
                if ($useEndpoint && !$form->process()) {
                    throw new AppException('Произошла ошибка. Обратитесь в службу поддержки');
                }
            } else {
                if (!\Yii::$app->request->isPost || !$form->load(\Yii::$app->request->post()) || !$form->process()) {
                    throw new AppException('Произошла ошибка. Обратитесь в службу поддержки');
                }
            }
            
            $model->import($form->data);
        } catch (\Exception $e) {            
            return $this->redirectBackWhenException($e);            
        }
        
        return $this->redirectWithSuccess(['developer/view', 'id' => $model->id], $this->getImportMessage($model));
    }
    
    /**
     * Check import data
     * 
     * @return mixed
     */
    public function actionCheckImport()
    {        
        $class = \Yii::$app->request->post('class');
        $type = \Yii::$app->request->post('type');
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        if (($result = \app\components\validators\ImportValidator::execute($class, $type)) < 0) {
            \Yii::$app->response->statusCode = 401;        
            \Yii::$app->response->data = [
                'message' => \app\components\validators\ImportValidator::$errorMessages[$result]
            ];
            return;
        }
    }
    
    /**
     * 
     * @param mixed $model
     * @return string
     */
    private function getImportMessage($model)
    {
        $newbuildingsComplexInfo = '';
        if ($model->insertedNewbuildingComplexesCount == 0 && $model->updatedNewbuildingComplexesCount == 0) {
            $newbuildingsComplexInfo = 'Жилые комплексы не изменены';
        } else {
            if ($model->insertedNewbuildingComplexesCount > 0) {
                $newbuildingsComplexInfo .= "{$model->insertedNewbuildingComplexesCount} жилых комплексов добавлено";
            }        
            if ($model->updatedNewbuildingComplexesCount > 0) {
                $newbuildingsComplexInfo .= "{$model->updatedNewbuildingComplexesCount} жилых комплексов обновлено";
            }
        }
        
        $newbuildingsInfo = '';
        if ($model->insertedNewbuildingsCount == 0 && $model->updatedNewbuildingsCount == 0) {
            $newbuildingsInfo = ', Позиции не изменены';
        } else {
            if ($model->insertedNewbuildingsCount > 0) {
                $newbuildingsInfo .= ", {$model->insertedNewbuildingsCount} позиций добавлено";
            }        
            if ($model->updatedNewbuildingsCount > 0) {
                $newbuildingsInfo .= ", {$model->updatedNewbuildingsCount} позиций обновлено";
            }
        }
        
        $flatsInfo = '';
        if ($model->insertedFlatsCount == 0 && $model->updatedFlatsCount == 0) {
            $flatsInfo = ', Квартиры не изменены';
        } else {
            if ($model->insertedFlatsCount > 0) {
                $flatsInfo .= ", {$model->insertedFlatsCount} квартир добавлено";
            }        
            if ($model->updatedFlatsCount > 0) {
                $flatsInfo .= ", {$model->updatedFlatsCount} квартир обновлено";
            }
        }
        
        return "Импорт выполнен: {$newbuildingsComplexInfo}{$newbuildingsInfo}{$flatsInfo}";
    }
}
