<?php

namespace app\commands;

use app\models\User;
use yii\console\Controller;

/**
 * Project initialization
 */
class InitController extends Controller
{
    /**
     * Project initialization
     */
    public function actionRun()
    { 
        $this->addFolders();
        $this->initRbac();
        $this->addUser();
        
        $this->addCommandToCron('favorite/archive');
        $this->addCommandToCron('offer/remove-expired');
    }
    
    /**
     * Add command to cron
     * 
     * @param string $command command which cron runs
     */
    public function addCommandToCron($command)
    {
        $yiiCommandPath = str_replace(' ', '\ ', \Yii::getAlias("@app/yii"));
        system("(crontab -l 2>/dev/null; echo \"* * * * * php $yiiCommandPath $command\") | crontab -");
    }
    
    /**
     * Add needed folders to project
     */
    private function addFolders()
    {
        $uploadFolder = \Yii::getAlias('@webroot/uploads');
        if ( !is_dir($uploadFolder) ) {
            mkdir($uploadFolder);
        }
    }

    /**
     * Add RBAC roles
     */
    private function initRbac()
    {
        $auth = \Yii::$app->authManager;

        $auth->removeAll();

        $admin = $auth->createRole('admin');
	    $manager = $auth->createRole('manager');
        $agent = $auth->createRole('agent');

        $auth->add($admin);
        $auth->add($manager);
        $auth->add($agent);
    }

    /**
     * Add needed users
     */
    private function addUser()
    {
        $user = new User();
        
        $user->first_name = "Egor";
        $user->middle_name = "Anatolevich";
        $user->last_name = "Sechin";
        $user->email = 'ts-working@yandex.ru';

        if ($user->save()) {
            $auth = \Yii::$app->authManager;
            $role = $auth->getRole('admin');
            $auth->assign($role, $user->id);
        }
    }
}
