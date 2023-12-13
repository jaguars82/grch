<?php

namespace app\components\actions;

use yii\base\Action;
use yii\helpers\ArrayHelper;

/**
 * Lists all models with search support
 */
class IndexWithSearch extends Action
{
    public $searchModelClass;
    public $view = NULL;
    
    /**
     * Execute action
     * 
     * @return Response
     */
    public function run()
    {
        $searchModel = new $this->searchModelClass();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        
        // return $this->controller->render(is_null($this->view) ? 'index' : $this->view, [
        return $this->controller->inertia($this->view, [
            'searchModel' => $searchModel,
            //'dataProvider' => $dataProvider,
            'dataProvider' => ArrayHelper::toArray($dataProvider->getModels()),
            'pagination' => [
                'page' => $dataProvider->getPagination()->getPage(),
                'totalPages' => $dataProvider->getPagination()->getPageCount()
            ],
            'itemsCount' => $searchModel->itemsCount,
        ]);
    }
}
