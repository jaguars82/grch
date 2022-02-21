<?php

namespace app\controllers\admin\office;

use Yii;
use app\models\Office;
use app\models\Developer;
use app\models\form\OfficeForm;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\traits\CustomRedirects;

/**
 * DeveloperOfficeController implements the CRUD actions for Office model.
 */
class DeveloperOfficeController extends Controller
{
    use CustomRedirects;

    public $layout = 'admin';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'roles' => ['admin', 'manager'],
                    ],
                ]
            ],
        ];
    }

    /**
     * Lists all Office models.
     * @return mixed
     */
    public function actionIndex($developerId)
    {
        if (($developer = Developer::findOne($developerId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $developer->getOffices(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'developer' => $developer,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Office model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($developerId)
    {
        if (($developer = Developer::findOne($developerId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        $form = new OfficeForm();
        $form->developer_id = $developer->id;

        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            $model = (new Office())->fill($form->attributes);
            $model->save();
            $model->link('developer', $developer);
            
            return $this->redirectWithSuccess(['admin/office/developer-office/index', 'developerId' => $developer->id], 'Добавлен офис');
        }

        return $this->render('create', [
            'model' => $form,
            'developer' => $developer
        ]);
    }

    /**
     * Updates an existing Office model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (($model = $this->findModel($id)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        $form = (new OfficeForm())->fill($model->attributes);
        $form->developer_id = $model->developer->id;

        if ($form->load(\Yii::$app->request->post()) && $form->validate()) {
            $model->fill($form->attributes);
            $model->save();

            return $this->redirectWithSuccess(['admin/office/developer-office/index', 'developerId' => $model->developer->id], 'Офис успешно обновлен');
        }

        return $this->render('update', [
            'office' => $model,
            'model' => $form,
        ]);
    }

    /**
     * Deletes an existing Office model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $developerId = $model->developer->id;

        $model->delete();

        return $this->redirectWithSuccess(['admin/office/developer-office/index', 'developerId' => $developerId], 'Офис удален');
    }

    /**
     * Finds the Office model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Office the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Office::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
