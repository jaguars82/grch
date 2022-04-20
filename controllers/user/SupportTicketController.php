<?php

namespace app\controllers\user;

use app\models\SupportTicket;
use app\models\SupportMessage;
use app\models\form\SupportTicketForm;
use app\models\form\SupportMessageForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class SupportTicketController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'create' => ['GET', 'POST']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create'],
                        'roles' => ['admin', 'manager', 'agent'],
                    ],
                ]
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCreate()
    {

        $ticket_model = new SupportTicketForm();
        $message_model = new SupportMessageForm();

        /*
        if ($ticket_model->load(\Yii::$app->request->post())) {
            if ($ticket_model->validate()) {
                // form inputs are valid, do something here
                return;
            }
        }
        */

        /**
         * 
         *  THE WORK AT THIS BRANCH (SUPPORT2) WORK HAS BEEN PAUSED ON THIS SECTION
         * 
         */
        /*
        if (\Yii::$app->request->isPost && 
            ($ticket_model->load(\Yii::$app->request->post())
            & $ticket_model->process() & $message_model->load(\Yii::$app->request->post())  & $message_model->process())
        ) {
            try {
                // Developer::create($form->attributes, $importForm->attributes);
                $transaction = \Yii::$app->db->beginTransaction();

                try {

                    $ticket = (new Newbuilding())->fill($newbuildingData);
                    $ticket->save();
        
                    $advantages = !empty($newbuildingData['advantages']) ? $newbuildingData['advantages'] : [];
        
                    foreach($advantages as $advantageId) {
                        if($advantage = Advantage::findOne($advantageId) === null) {
                            throw new NotFoundHttpException('Данные отсутсвуют');
                        }
                        $newbuilding->link('advantages', $advantage);
                    }
        
                    $transaction->commit();
                } catch(\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }

                return $newbuilding;

            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['index'], 'Добавлен застройщик');
        }
        */

        return $this->render('_form', [
            'ticket_model' => $ticket_model,
            'message_model' => $message_model,
        ]);

        // return $this->render('create');
    }
}
