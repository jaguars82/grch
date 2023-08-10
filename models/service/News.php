<?php

namespace app\models\service;

use Yii;
use app\models\NewsFile;
use app\models\ActionData;
use app\models\NewbuildingComplex;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for processing news.
 */
class News extends \app\models\News
{
    /**
     * Create news's entities
     * 
     * @param array $newsData
     * @param array $actionData
     * @return \app\models\service\News
     * @throws \Exception
     */
     public static function create($newsData, $actionData, $actionFlatData = null)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $news = (new News())->fill($newsData);
            $news->save();

            foreach ($newsData['files'] as $savedFile => $file) {
                $newsFile = new NewsFile([
                    'news_id' => $news->id,
                    'name' => $file,
                    'saved_name' => $savedFile,
                ]);
                $newsFile->save();
            }

            if (!self::checkDeveloperForNewbuildingComplexes($newsData['newbuildingComplexes'])) {
                throw new \Exception('Given newbuilding complexes belong different developers');
            }
            
            foreach (NewbuildingComplex::findAll($newsData['newbuildingComplexes']) as $newbuildingComplex) {
                $news->link('newbuildingComplexes', $newbuildingComplex);
            }
            
            if ($news->isAction()) {
                if ($actionFlatData != null) {
                    $actionData['flat_filter'] = json_encode($actionFlatData->flatFilter);
                } else {
                    $actionData['flat_filter'] = json_encode(null);
                }
    
                $actionDataObject = new ActionData();
                $actionDataObject->fill($actionData);
    
                if ($actionFlatData != null && isset($actionData['discount_type']) && !empty($actionData['discount_type'])) {
                    
                    $discount = self::setDiscountField($actionData);
                    $actionFlatData->setDiscount($discount, $news, false, $actionData['discount_type']);
                }
                $news->link('actionData', $actionDataObject);
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            echo '<pre>'; var_dump($e); echo '</pre>'; die;
            throw $e;
        }
        
        return $news;
    }
    
    /**
     * Edit news's entities
     * 
     * @param array $newsData
     * @param array $actionData
     * @throws \Exception
     */
    public function edit($newsData, $actionData, $actionFlatData = null)
    {
        $transaction = Yii::$app->db->beginTransaction();
            
        try {
            $this->fill($newsData, ['image']);
            
            if ($newsData['is_image_reset']) {
                $this->image = NULL;
            } else {
                $this->image = (!is_null($newsData['image'])) ? $newsData['image'] : $this->image; 
            }
                           
            $this->save();

            foreach ($this->newsFiles as $newsFile) {
                if (!in_array(strval($newsFile->id), $newsData['savedFiles'])) {
                    $newsFile->delete();
                }
            }

            foreach ($newsData['files'] as $savedFile => $file) {
                $newsFile = new NewsFile([
                    'news_id' => $this->id,
                    'name' => $file,
                    'saved_name' => $savedFile,
                ]);
                $newsFile->save();
            }
            
            if (!self::checkDeveloperForNewbuildingComplexes($newsData['newbuildingComplexes'])) {
                throw new \Exception('Given newbuilding complexes belong different developers');
            }
            
            $newbuildingComplexes = ArrayHelper::getColumn($this->getnewbuildingComplexes()->asArray()->all(), 'id');
            $receivedNewbuildingComplexes = is_array($newsData['newbuildingComplexes']) ? $newsData['newbuildingComplexes'] : [];
            
            $addedNewbuildingComplexes = NewbuildingComplex::findAll(array_diff($receivedNewbuildingComplexes, $newbuildingComplexes));
            foreach ($addedNewbuildingComplexes as $newbuildingComplex) {
                $this->link('newbuildingComplexes', $newbuildingComplex);
            }

            $removedNewbuildingComplexes = NewbuildingComplex::findAll(array_diff($newbuildingComplexes, $receivedNewbuildingComplexes));
            foreach ($removedNewbuildingComplexes as $newbuildingComplex) {
                $this->unlink('newbuildingComplexes', $newbuildingComplex, true);
            }
            
            if ($this->isAction()) {
                if (is_null($this->actionData)) {
                    $actionData['flat_filter'] = json_encode($actionFlatData->flatFilter);
                    $actionDataObject = new ActionData();
                    $actionDataObject->fill($actionData);
                    if ($actionFlatData != null && !empty($actionData['discount_type'])) {
                        $discount = self::setDiscountField($actionData);
                        $actionFlatData->setDiscount($discount, $this, true, $actionData['discount_type']);
                    }
                    $this->link('actionData', $actionDataObject);
                } else {
                    $actionData['flat_filter'] = json_encode($actionFlatData->flatFilter);
                    $this->actionData->fill($actionData);
                    
                    if ($actionFlatData != null && !empty($actionData['discount_type'])) {
                        switch($actionData['discount_type']) {
                            case 0:
                                $discount = $actionData['discount'];
                                break;
                            case 1:
                                $discount = $actionData['discount_amount'];
                                break;
                            case 2:
                                $discount = $actionData['discount_price'];
                                break;
                        }

                        $actionFlatData->setDiscount($discount, $this, true, $actionData['discount_type']);
                    }
                    /** field 'discount' can not be NULL */
                    if (empty($this->actionData->discount)) {
						$this->actionData->discount = 0;
					}
                    $this->actionData->save();
                }
                
            } else {
                if (!is_null($this->actionData)) {
                    $this->unlink('actionData', $this->actionData, true);
                }
            }

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    /**
     * Check that newbuilding complex/es belongs to same developer
     * 
     * @param mixed $newbuildingComplexes newbuilding complex/es
     * @return boolean
     */
    private static function checkDeveloperForNewbuildingComplexes($newbuildingComplexes)
    {
        $newbuildingComplexesArray = is_array($newbuildingComplexes) ? $newbuildingComplexes : [$newbuildingComplexes];
        $result = NewbuildingComplex::find()
            ->select(['developer_id'])
            ->where(['in', 'id', $newbuildingComplexesArray])
            ->groupBy('developer_id')
            ->all();
        return count($result) <= 1;
    }

    private static function setDiscountField($data)
    {
        switch ($data['discount_type']) {
            case 0:
                $discount = $data['discount'];
                break;
            case 1:
                $discount = $data['discount_amount'];
                break;
            case 2:
                $discount = $data['discount_price'];
                break;
        }
        return $discount;
    }
}
