<?php

namespace app\controllers\admin;

use app\components\exceptions\AppException;
use app\components\traits\CustomRedirects;
use app\models\Bank;
use app\models\Developer;
use app\models\Region;
use app\models\City;
use app\models\StreetType;
use app\models\BuildingType;
use app\models\Advantage;
use app\models\Newbuilding;
use app\models\form\NewbuildingComplexForm;
use app\models\form\ProjectDeclarationForm;
use app\models\search\NewbuildingComplexSearch;
use app\models\search\NewbuildingComplexFlatSearch;
use app\models\service\NewbuildingComplex;
use app\models\Archive;
use app\models\form\ArchiveForm;
use app\components\flat\ImportArchive;
use app\components\flat\SvgDom;
use app\components\archive\services\ZipService;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * NewbuildingComplexController implements the CRUD actions for NewbuildingComplex model.
 */
class NewbuildingComplexController extends Controller
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
                    'upload-project-declaration' => ['POST'],
                    'remove-project-declaration' => ['POST'],
                    'upload-archive' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'index', 'create', 'update', 'delete', 'upload-archive', 
                            'import-archive', 'download-archive',
                        ],
                        'roles' => ['admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['remove-project-declaration', 'upload-project-declaration', 'download-project-declaration'],
                        'roles' => ['@'],
                    ],
                ]
            ],
        ];
    }
    
    /**
     * Lists all NewbuildingComplex models.
     * 
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NewbuildingComplexSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams, false);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'itemsCount' => $searchModel->itemsCount,
            'developers' => Developer::getAllAsList(),
        ]);
    }

    /**
     * Creates a new NewbuildingComplex model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * 
     * @param integer $developerId
     * @return mixed
     */
    public function actionCreate($developerId)
    {
        if (($developer = Developer::findOne($developerId)) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }        
        
        $form = new NewbuildingComplexForm();
        $form->developer_id = $developer->id;

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {            
            try {
                NewbuildingComplex::create($form->attributes);
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['admin/newbuilding-complex/index',  'NewbuildingComplexSearch[developer_id]' => $developer->id], 'Добавлен жилой комплекс');
        }

        return $this->render('create', [
            'newbuildingComplex' => $form,
            'developer' => $developer,
            'regions' => Region::getAllAsList(),
            'cities' => !is_null($form->region_id) ? City::find()->forRegion($form->region_id)->all() : [],
            'streetTypes' => StreetType::getAllAsList(),
            'buildingTypes' => BuildingType::getAllAsList(),
            'advantages' => Advantage::getAllAsList(),
        ]);
    }

    /**
     * Updates an existing NewbuildingComplex model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {        
        $newbuildingComplex = $this->findModel($id);
        $form = (new NewbuildingComplexForm())->fill($newbuildingComplex->attributes);
        $form->logo = $newbuildingComplex->logo;
        $form->remove_project_declaration = (int)!is_null($newbuildingComplex->project_declaration);
        $form->savedBanks = ArrayHelper::getColumn($newbuildingComplex->getBanks()->asArray()->all(), 'id');
        $form->stages = $newbuildingComplex->getStages()->asArray()->all();
        $form->advantages = ArrayHelper::getColumn($newbuildingComplex->getAdvantages()->asArray()->all(), 'id');
        $form->scenario = NewbuildingComplexForm::SCENARIO_UPDATE;

        $savedImages = $newbuildingComplex->getImages()->asArray()->all();
        $banks = Bank::find()->all();

        $bankTariffs = [];
        foreach ($banks as $bank) {
            $bankTariffs[$bank->id] = $bank->getTariffs()->asArray()->all();
        }

        $canImportArchive = false;
        if($newbuildingComplex->archive !== null && $newbuildingComplex->archive->checked && $newbuildingComplex->flats !== null) {
            $canImportArchive = true;
        }

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post()) && $form->process()) {
            try {               
                $newbuildingComplex->edit($form->attributes);
            } catch (\Exception $e) {
                return $this->redirectBackWhenException($e);
            }

            return $this->redirectWithSuccess(['update', 'id' => $newbuildingComplex->id], 'Жилой комплекс обновлен');
        }

        return $this->render('update', [
            'model' => $newbuildingComplex,
            'newbuildingComplex' => $form,
            'newbuildingComplexId' => $newbuildingComplex->id,
            'developer' => $newbuildingComplex->developer,
            'banks' => $banks,
            'bankTariffs' => $bankTariffs,
            'regions' => Region::getAllAsList(),
            'cities' => !is_null($newbuildingComplex->region_id) ? City::find()->forRegion($newbuildingComplex->region_id)->all() : [],
            'streetTypes' => StreetType::getAllAsList(),
            'buildingTypes' => BuildingType::getAllAsList(),
            'advantages' => Advantage::getAllAsList(),
            'archiveForm' => new ArchiveForm(),
            'canImportArchive' => $canImportArchive,
            'projectDeclaration' => new ProjectDeclarationForm(),
            'savedImages' => $savedImages
        ]);
    }

    /**
     * Deletes an existing NewbuildingComplex model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * 
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $developerId = $model->developer->id;
        
        try {
            $model->remove();
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }

        return $this->redirectWithSuccess(['admin/newbuilding-complex/index',  'NewbuildingComplexSearch[developer_id]' => $developerId], 'Жилой комплекс удален');
    }
    
    /**
     * Finds the NewbuildingComplex model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer $id
     * @return NewbuildingComplex the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $withRelatedModels = false)
    {
        $query = NewbuildingComplex::find()->where(['id' => $id]);
        
        if ($withRelatedModels) {
            $query->with(['furnishes.furnishImages']);
        }
        
        if (($model = $query->one()) === null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        return $model;
    }

    /**
     * Add project declaration for given newbuilding complex.
     * 
     * @param type $id
     * @return type
     * @throws \Exception
     */
    public function actionUploadProjectDeclaration($id)
    {
        try {
            $model = $this->findModel($id);
            $form = new ProjectDeclarationForm();

            if ($form->load(\Yii::$app->request->post()) && $form->process()) {
                $projectDeclaration = $model->project_declaration;
                $model->project_declaration = $form->file;
                $model->save();
                
                if (!is_null($projectDeclaration)) {
                    unlink(\Yii::getAlias('@webroot/uploads')."/{$projectDeclaration}");
                }
            } else {
                throw new \Exception('Form loading or processing error');
            }
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }
        
        return $this->redirectWithSuccess(NULL, 'Проектная декларация загружена');
    }
    
    /**
     * Getting project declaration's file for given newbuilding complex.
     * 
     * @param integer $id Newbuilding complex's ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDownloadProjectDeclaration($id)
    {
        $model = $this->findModel($id);
        
        if (empty($model->project_declaration)) {
            throw new NotFoundHttpException("Newbuilding complex doesn't nave the project declaration");
        }
        
        $fileName = \Yii::getAlias("@webroot/uploads/{$model->project_declaration}");
        return \Yii::$app->response->sendFile($fileName, 'Проектная декларация - ' . $model->name . '.' . pathinfo($fileName, PATHINFO_EXTENSION));
    }
    
    /**
     * Remove project declaration for given newbuilding complex.
     * 
     * @param type $id
     * @return type
     */
    public function actionRemoveProjectDeclaration($id)
    {
        try {
            $model = $this->findModel($id);
            $projectDeclaration = $model->project_declaration;
            $model->project_declaration = NULL;
            $model->save();
            
            if (!is_null($projectDeclaration)) {
                unlink(\Yii::getAlias('@webroot/uploads')."/{$projectDeclaration}");
            }
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        }
        
        return $this->redirectWithSuccess(NULL, 'Проектная декларация удалена');
    }

    /**
     * Upload archive and check
     * 
     * @param integer $newbuildingComplexId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUploadArchive($newbuildingComplexId) 
    {
        if(($newbuildingComplex = $this->findModel($newbuildingComplexId)) == null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }

        $archiver = new ZipService(\Yii::getAlias(Archive::TEMP_PATH));
        try {
            $form = new ArchiveForm();

            if ($form->load(\Yii::$app->request->post()) && $form->process()) {
                $archive = $newbuildingComplex->archive;
                if($archive === null) {
                    $archive = new Archive();
                } else {
                    $archive->scenario = Archive::SCENARIO_UPDATE;
                }

                $archive->fill($form->attributes);
                $extractPath = \Yii::getAlias(Archive::FILE_PATH) . $archive->file;

                if($archiver->extract($extractPath)) {
                    $import = new ImportArchive($archiver, $newbuildingComplex);
                    
                    if(!$import->check()) {
                        $archive->checked = false;

                        \Yii::$app->session->setFlash('archive_error', implode('<br>', $import->getErrors()));
                    } else {
                        $archive->checked = true;
                    }

                    if($archive->validate() && $archive->save() && $archive->scenario != Archive::SCENARIO_UPDATE) {
                        $newbuildingComplex->link('archive', $archive);
                    }

                    if($archive->checked) {
                        return $this->redirectWithSuccess(NULL, 'Архив успешно прошел проверку');
                    } else {
                        return $this->redirectWithError(NULL, 'Архив не прошел проверку');
                    }
                } else {
                    throw new AppException('Ошибка при распаковке архива');
                }
            } else {
                throw new \Exception('Form loading or processing error');
            }
        } catch (\Exception $e) {
            return $this->redirectBackWhenException($e);
        } finally {
            $archiver->clean();
        }
    }

    /**
     * Import archive to database
     * 
     * @param integer $newbuildingComplexId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionImportArchive($newbuildingComplexId)
    {
        if(($newbuildingComplex = $this->findModel($newbuildingComplexId)) == null) {
            throw new NotFoundHttpException('Данные отсутсвуют');
        }
        try {   
            $archive = $newbuildingComplex->archive;
            if($archive === null) {
                throw new AppException('Архив не найден');
            }

            if(!$archive->checked) {
                throw new AppException('Архив не прошел проверку');
            }
    
            $archiver = new ZipService(\Yii::getAlias(Archive::TEMP_PATH));
            $extractPath = \Yii::getAlias(Archive::FILE_PATH) . $archive->file;

            if($archiver->extract($extractPath)) {
                $import = new ImportArchive($archiver, $newbuildingComplex);
                
                $importResult = $import->upload();
                if($importResult === false) {
                    throw new AppException('Ошибка при импорте архива');
                }

                foreach($importResult as $key => $layoutData) {
                    $nodeList = [];
   
                    foreach($layoutData['layout_coords'] as $coord) {
                        $nodeList[] = [
                            'name' => 'polygon',
                            'attributes' => [
                                'style' => 'fill: rgba(0, 128, 1, 0.3); stroke: purple; stroke-width: 5px',
                                'points' => $coord
                            ]
                        ];
                    }
   
                    $svgDom = new SvgDom(\Yii::getAlias('@webroot/uploads') . '/' . $layoutData['floor_layout']);
                    $svgDom->appendNodes($nodeList);
   
                    $importResult[$key]['html_layout'] = $svgDom->getFileContent();
                }
                
                return $this->render('import-archive', [
                    'model' => $newbuildingComplex, 
                    'developer' => $newbuildingComplex->developer,
                    'result' => ArrayHelper::index($importResult, null, 'position')
                ]);
            } else {
                throw new AppException('Не удалось распаковать архив');
            }
        } catch(\Exception $e) {
            return $this->redirectBackWhenException($e);
        } finally {
            $archiver->clean();
        }

        return $this->redirectWithSuccess(\Yii::$app->request->referrer, 'Архив успешно импортирован');
    }

    /**
     * Download last uploaded archive
     * 
     * @param integer $newbuildingComplexId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDownloadArchive($newbuildingComplexId)
    {
        $model = $this->findModel($newbuildingComplexId);
        
        if (empty($model->archive)) {
            throw new NotFoundHttpException("Newbuilding complex doesn't nave the archive");
        }
        
        $fileName = \Yii::getAlias("@webroot/uploads/archive/{$model->archive->file}");
        return \Yii::$app->response->sendFile($fileName, 'Архив - ' . $model->name . '.' . pathinfo($fileName, PATHINFO_EXTENSION));
    }
}
