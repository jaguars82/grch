<?php

namespace app\components\actions;

use app\components\exceptions\AppException;
use app\models\form\ImportForm;
use yii\base\Action;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Action for import flat's data from excel file
 */
class Import extends Action
{
    public $model;
    public $useEndpoint = false;
    
    /**
     * Execute action
     * 
     * @param integer $id object ID
     * @return Response
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function run($id)
    {
        if (($model = $this->model::findOne($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }
        
        if ($this->useEndpoint && (is_null($model->import) || empty($model->import->endpoint))){
            \Yii::$app->session->setFlash('error', 'На заданы настройки импорта');
            return $this->controller->redirect(\Yii::$app->request->referrer); 
        }
        
        $form = new ImportForm();
        $form->importObject = $model->import;        
        $form->endpoint = $this->useEndpoint ? $model->import->endpoint : NULL;
        
        try {
            if ($this->useEndpoint) {
                if ($this->useEndpoint && !$form->process()) {
                    throw new AppException('Произошла ошибка. Обратитесь в службу поддержки');
                }
            } else {
                if (!\Yii::$app->request->isPost || !$form->load(\Yii::$app->request->post()) || !$form->process()) {
                    throw new AppException('Произошла ошибка. Обратитесь в службу поддержки');
                }
            }

            $model->import($form->data);
        } catch (\Exception $e) {
            $message = ($e instanceof AppException) ? $e->getMessage() : 'Произошла ошибка. Обратитесь в службу поддержки';
            
            if (\Yii::$app->request->isAjax) {
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                \Yii::$app->response->statusCode = 400;
                \Yii::$app->response->data = [
                    'message' => $message,
                ];

                return;
            }
        
            \Yii::$app->session->setFlash('error', $message);
            return $this->controller->redirect(\Yii::$app->request->referrer);
        }
        
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
        
        $message = "Импорт выполнен: {$newbuildingsComplexInfo}{$newbuildingsInfo}{$flatsInfo}";
        
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;        
            \Yii::$app->response->data = [
                'message' => $message,
            ];
            
            return;
        }
        
        \Yii::$app->session->setFlash('success', $message);
        return $this->controller->redirect(['view', 'id' => $model->id]);
    }
}
