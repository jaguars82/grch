<?php

namespace app\components\widgets;

use yii\base\Widget;

/**
 * Widget for view flats in chess form
 */
class LoadMore extends Widget
{
    public $totalCount;
    public $pageSize;
    public $containerId;
    public $type;
    
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->render('/widgets/load-more', [
            'totalCount' => $this->totalCount,
            'pageSize' => $this->pageSize,
            'pageCount' => ceil($this->totalCount / $this->pageSize),
            'containerId' => $this->containerId,
            'type' => $this->type,
        ]);
    }
}
