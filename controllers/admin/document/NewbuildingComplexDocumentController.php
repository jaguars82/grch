<?php

namespace app\controllers\admin\document;

use Yii;
use app\models\Document;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\traits\CustomRedirects;
use app\models\form\DocumentForm;
use app\models\NewbuildingComplex;

/**
 * NewbuildingComplexDocumentController implements the CRUD actions for Document model.
 */
class NewbuildingComplexDocumentController extends Controller
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
        ];
    }

    /**
     * Lists all Document models.
     * @return mixed
     */
    public function actionIndex($newbuildingComplexId)
    {
        if (($newbuildingComplex = NewbuildingComplex::findOne($newbuildingComplexId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $newbuildingComplex->hasMany(Document::className(), ['id' => 'document_id'])
            ->viaTable('newbuilding_complex_document', ['newbuilding_complex_id' => 'id']),
        ]);

        return $this->render('index', [
            'newbuildingComplex' => $newbuildingComplex,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Document model.
     * @return mixed
     */
    public function actionCreate($newbuildingComplexId)
    {
        if (($newbuildingComplex = NewbuildingComplex::findOne($newbuildingComplexId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }
        
        $form = new DocumentForm();
        $form->newbuilding_complex_id = $newbuildingComplex->id;

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {         
            try {
                $model = (new Document())->fill($form->attributes);
                $model->save();
                $newbuildingComplex->link('documents', $model);                
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }               

            return $this->redirectWithSuccess(['index', 'newbuildingComplexId' => $newbuildingComplex->id], 'Добавлен документ');
        }

        return $this->render('create', [
            'model' => $form,
            'newbuildingComplex' => $newbuildingComplex,
        ]);
    }

    /**
     * Updates an existing Document model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $form = (new DocumentForm())->fill($model->attributes);
        $form->scenario = DocumentForm::SCENARIO_UPDATE;

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {            
            try {
                $model->fill($form->attributes, ['file']);
                $model->file = (!is_null($form->file)) ? $form->file : $model->file;
                $model->save();
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['index', 'newbuildingComplexId' => $model->newbuildingComplex->id], 'Документ обновлен');
        }

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $form,
            'document' => $model,
            'newbuildingComplex' => $model->newbuildingComplex,
        ]);
    }

    /**
     * Deletes an existing Document model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $newbuildingComplexId = $model->newbuildingComplex->id;
        
        try {
            $model->delete();
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }
        
        return $this->redirectWithSuccess(['index', 'newbuildingComplexId' => $newbuildingComplexId], 'Документ удален');
    }

    /**
     * Finds the Document model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Document the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Document::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
