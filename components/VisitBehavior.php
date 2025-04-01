<?php

namespace app\components;

use Yii;
use yii\base\Behavior;
use yii\web\Controller;

class VisitBehavior extends Behavior
{
    /**
     * Roots we don't log
     */
    private array $ignoredRoutes = [
        // 'site/index', - the template
    ];

    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'logVisit',
        ];
    }

    public function logVisit()
    {
        $route = Yii::$app->controller->getRoute();
        if (in_array($route, $this->ignoredRoutes, true)) {
            return;
        }

        Yii::$app->visitLogger->logVisit(false);
    }
}
