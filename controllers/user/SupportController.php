<?php

namespace app\controllers\user;

use app\components\traits\CustomRedirects;
use app\components\SharedDataFilter;
use app\models\SupportTicket;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use tebe\inertia\web\Controller;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class SupportController extends Controller
{
    use CustomRedirects;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET', 'POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['admin', 'manager', 'agent', 'developer_repres'],
                    ],
                ]
            ],
            [
                'class' => SharedDataFilter::class
            ],
        ];
    }

    public function actionIndex()
    {
        $query = SupportTicket::find(['<>', 'is_archived', 1]);

        if (\Yii::$app->user->identity->role === 'agent' || \Yii::$app->user->identity->role === 'manager' || \Yii::$app->user->identity->role === 'developer_repres') {
            $query->andWhere(['author_id' => \Yii::$app->user->id]);
        }

        // get the total number of support tickets
        $count = $query->count();

        // create a pagination object with the total count
        $pagination = new Pagination(['totalCount' => $count]);

        if (!empty(\Yii::$app->request->get('psize'))) {
            $pagination->setPageSize(\Yii::$app->request->get('psize'));
        }

        $tickets = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['updated_at' => SORT_DESC])
            ->all();
        
        //$model = new SupportTicket();

        if(\Yii::$app->user->can('admin')) {
            //$tickets = $model->getAllTickets();

            foreach($tickets as $key => $ticket) {
                $ticket->setUnreadFromAuthor();
                $ticket->setAuthorName();
                $ticket->setAuthorSurname();
                $ticket->setAuthorAvatar();
                $ticket->setAuthorRole();
                $ticket->setAuthorAgency();
            }            
        } else {
            //$tickets = $model->getTicketsByAuthor(\Yii::$app->user->id);

            foreach($tickets as $key => $ticket) {
                $ticket->setUnreadFromAdmin();
            }
        }
        
        //echo '<pre>'; var_dump($tickets); echo '</pre>'; die();

        return $this->inertia('User/Support/Index', [
            'user' => \Yii::$app->user->identity,
            'tickets' => ArrayHelper::toArray($tickets),
            'totalRows' => $count,
            'page' => $pagination->page,
            'psize' => $pagination->pageSize,
        ]);
    }
}