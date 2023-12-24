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
    public $oldAdminPanel = false;
    
    /**
     * Execute action
     * 
     * @return Response
     */
    public function run()
    {
        $searchModel = new $this->searchModelClass();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        if ($this->oldAdminPanel) { // this section returns views for old admin-panel (TODO: remove the section after migration to new admin-panel)
            return $this->controller->render(is_null($this->view) ? 'index' : $this->view, [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'itemsCount' => $searchModel->itemsCount,
            ]);
        } else { // this section returns views for new front app (Vue)
            return $this->controller->inertia($this->view, [
                'searchModel' => $searchModel,
                'dataProvider' => ArrayHelper::toArray($dataProvider->getModels()),
                'pagination' => [
                    'page' => $dataProvider->getPagination()->getPage(),
                    'totalPages' => $dataProvider->getPagination()->getPageCount()
                ],
                'itemsCount' => $searchModel->itemsCount,
            ]);
        }

    }
}
