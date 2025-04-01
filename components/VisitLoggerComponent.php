<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\db\Expression;
use app\models\VisitLog;

class VisitLoggerComponent extends Component
{
    public $visitTimeout = 600; // 10 min - interval between repeating visiting the same page

    /**
     * @param bool $isLogin
     */
    public function logVisit($isLogin = false)
    {
        $request = Yii::$app->request;

        if (!$isLogin && !$request->isGet) {
            return; // Log only GET-requests and authorizations
        }

        $user = Yii::$app->user;
        if ($user->isGuest) {
            return; // Log users only
        }

        $userId = $user->id;
        $route = $isLogin ? 'user/login' : Yii::$app->controller->route;
        $requestParams = !empty($request->get()) ? json_encode($request->get(), JSON_UNESCAPED_UNICODE) : null;
        $ipAddress = Yii::$app->request->userIP;
        $userAgent = Yii::$app->request->userAgent;
        $deviceType = $this->detectDeviceType($userAgent);
        $referrer = $request->referrer ?? null;
        $currentTime = new Expression('NOW()');

        // Check if the has been a recent visit to the page
        $recentVisit = VisitLog::find()
            ->where([
                'user_id' => $userId,
                'route' => $route,
                'ip_address' => $ipAddress,
            ])
            ->andWhere(['>', 'visited_at', new Expression("NOW() - INTERVAL {$this->visitTimeout} SECOND")])
            ->exists();

        if (!$recentVisit) {
            // Create a new visit entry
            $visit = new VisitLog();
            $visit->user_id = $userId;
            $visit->route = $route;
            $visit->request_params = $requestParams;
            $visit->visited_at = $currentTime;
            $visit->ip_address = $ipAddress;
            $visit->user_agent = $userAgent;
            $visit->device_type = $deviceType;
            $visit->referrer = $referrer;
            $visit->is_login = $isLogin ? 1 : 0;
            $visit->save();
        }
    }

    /**
     * Detect the device-type using User-Agent.
     * @param string|null $userAgent
     * @return string
     */
    private function detectDeviceType($userAgent)
    {
        if (!$userAgent) {
            return VisitLog::DEVICE_DESKTOP;
        }

        $mobileKeywords = ['Mobile', 'Android', 'Silk/', 'Kindle', 'BlackBerry', 'Opera Mini', 'Opera Mobi', 'iPhone', 'iPad'];

        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return VisitLog::DEVICE_MOBILE;
            }
        }

        return VisitLog::DEVICE_DESKTOP;
    }
}
