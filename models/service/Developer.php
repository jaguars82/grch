<?php

namespace app\models\service;

use app\components\exceptions\AppException;
use app\models\Import;
use app\models\CompositeFlat;
use app\models\Flat;
use app\models\FloorLayout;
use app\models\Newbuilding;
use app\models\NewbuildingComplex;
use yii\httpclient\Client;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for processing developers.
 */
class Developer extends \app\models\Developer
{
    /**
     * Count of updated newbuilding complexes
     *
     * @var int
     */
    public $updatedNewbuildingComplexesCount = 0;

    /**
     * Count of inserted newbuilding complexes
     *
     * @var int
     */
    public $insertedNewbuildingComplexesCount = 0;

    /**
     * Count of updated newbuildings
     *
     * @var int
     */
    public $updatedNewbuildingsCount = 0;

    /**
     * Count of inserted newbuildings
     *
     * @var int
     */
    public $insertedNewbuildingsCount = 0;

    /**
     * Count of updated flats
     *
     * @var int
     */
    public $updatedFlatsCount = 0;

    /**
     * Count of inserted flats
     *
     * @var int
     */
    public $insertedFlatsCount = 0;

    /**
     * Create developer's entities
     *
     * @param array $developerData
     * @param array $importData
     * @return app\models\service\Developer
     * @throws \Exception
     */
    public static function create($developerData, $importData)
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $developer = (new Developer())->fill($developerData);
            
            if (!is_null($importData['type'])) {
                $import = (new Import())->fill($importData);
                $import->save();
                $developer->link('import', $import);
            } else {
                $developer->save();
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $developer;
    }

    /**
     * Edit developer's entities
     *
     * @param array $developerData
     * @param array $importData
     * @throws \Exception
     */
    public function edit($developerData, $importData)
    {
        $transaction = \Yii::$app->db->beginTransaction();

        $editedFields = [];
        foreach($this->attributes as $name => $value) {
            if(isset($developerData[$name]) && $developerData[$name] != $value) {
                $editedFields[] = $name;
            }
        }

        if(is_null($this->edited_fields) || empty($this->edited_fields)) {
            $this->edited_fields = [];
        }

        $this->edited_fields = array_merge($this->edited_fields, $editedFields);

        try {
            $this->fill($developerData, ['logo']);
            $this->logo = (!is_null($developerData['logo'])) ? $developerData['logo'] : $this->logo;
            $this->save();

            if (!is_null($importData['type'])) {
                if (!is_null($this->import)) {
                    $this->import->fill($importData);
                    $this->import->save();
                } else {
                    $import = (new Import())->fill($importData);
                    $import->save();
                    $this->link('import', $import);
                }
            } else {
                if (!is_null($this->import)) {
                    $this->import->delete();
                }
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Remove developer's entities
     *
     * @throws AppException
     */
    public function remove()
    {
        if (count($this->newbuildingComplexes)) {
            throw new AppException('Застройщик не может быть удален, так как имеет жилые комплексы');
        }

        if (!is_null($this->import)) {
            $this->import->delete();
        }

        $this->delete();
    }

    /**
     * Import flat's data from array getting from parsed sources
     *
     * @param array $data array with flat's data
     * @throws \Exception
     */
    public function import($data = NULL)
    {
        if (is_null($data)) {
            return;
        }

        $transaction = \Yii::$app->db->beginTransaction();

        try {
            if (!isset($data['newbuildingComplexes'])) {
                throw new AppException('Отсутсвуют данные о жилых комплексах');
            }

            if (!is_null($this->import)) {
                $this->import->touch('imported_at');
            }

            $newbuildingComplexes = $this->insertOrUpdateNewbuildingComplexes($data['newbuildingComplexes']);
            $newbuildings = $this->insertOrUpdateNewbuilding($newbuildingComplexes, $data['newbuildings']);

            if (isset($data['floorLayouts'])) {
                $this->insertFloorLayouts($newbuildings, $data['floorLayouts']);
            }

            $this->insertOrUpdateFlats($newbuildings, $data['flats']);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Insert new newbuilding complexes and update changed newbuilding complexes
     *
     * @param array $newbuildingComplexesData newbuilding complex data
     * @return NewbuildingComplex
     */
    private function insertOrUpdateNewbuildingComplexes($newbuildingComplexesData)
    {
        $newbuildingComplexes = [];
        $savedNewbuildingComplex = $this->getNewbuildingComplexes()->indexBy('feed_name')->all();

        foreach($newbuildingComplexesData as $key => $newbuildingComplexData) {
            if (array_key_exists($newbuildingComplexData['name'], $savedNewbuildingComplex)) {
                $newbuildingComplex = $savedNewbuildingComplex[$newbuildingComplexData['name']];
            } else {
                $newbuildingComplexData['feed_name'] = $newbuildingComplexData['name'];

                $newbuildingComplex = new NewbuildingComplex($newbuildingComplexData);
                $newbuildingComplex->developer_id = $this->id;
                $newbuildingComplex->save();
                $this->insertedNewbuildingComplexesCount++;
            }

            $newbuildingComplexes[$key] = $newbuildingComplex;
        }

        return $newbuildingComplexes;
    }

    /**
     * Insert new newbuildings and update changed newbuildings
     *
     * @param array $newbuildingComplexes newbuilding complexes array
     * @param array $newbuildingsData newbuildings data
     * @return Newbuilding
     */
    private function insertOrUpdateNewbuilding($newbuildingComplexes, $newbuildingsData)
    {
        $newbuildings = [];

        $savedNewbuildings = [];
        foreach($newbuildingComplexes as $key => $newbuildingComplex) {
            $savedNewbuildings[$key] = $newbuildingComplex->getNewbuildings()->indexBy('feed_name')->all();
        }

        foreach($newbuildingsData as $key => $newbuildingData) {
            $objectId = $newbuildingData['objectId'];
            unset($newbuildingData['objectId']);
            $newbuildingComplex = $newbuildingComplexes[$objectId];

            if (array_key_exists($newbuildingData['name'], $savedNewbuildings[$objectId])) {
                $newbuilding = $savedNewbuildings[$objectId][$newbuildingData['name']];

                if (isset($newbuildingData['deadline']) && $newbuildingData['deadline'] != $newbuilding->deadline) {
                    $newbuilding->fill($newbuildingData);
                    $newbuilding->save();
                    $this->updatedNewbuildingsCount++;
                }
            } else {
                $newbuildingData['feed_name'] = $newbuildingData['name'];

                $newbuilding = new Newbuilding($newbuildingData);
                $newbuilding->link('newbuildingComplex', $newbuildingComplex);
                $this->insertedNewbuildingsCount++;
            }

            $newbuildings[$key] = $newbuilding;
        }

        return $newbuildings;
    }

    /**
     * Insert new floor layouts
     *
     * @param array $newbuildings newbuildings array
     * @param array $floorLayoutsData floor layouts data
     */
    private function insertFloorLayouts($newbuildings, $floorLayoutsData)
    {
        $compositeFlats = [];

        $savedFlats = [];
        foreach($newbuildings as $key => $newbuilding) {
            $savedFloorLayouts[$key] = $newbuilding->getFloorLayouts()->all();
        }

        foreach($floorLayoutsData as $floorLayoutData) {
            $houseId = $floorLayoutData['houseId'];
            unset($floorLayoutData['houseId']);

            if (!($floorLayout = $this->checkFloorLayoutExists($savedFloorLayouts[$houseId], $floorLayoutData))) {
                $floorLayout = new FloorLayout($floorLayoutData);
                $floorLayout->newbuilding_id = $newbuildings[$houseId]->id;
                $floorLayout->image = $this->downloadFile($floorLayoutData['image']);
                $floorLayout->save(false);
            }
        }
    }

    /**
     * Insert new flats and update changed flats
     *
     * @param array $newbuildings newbuildings array
     * @param array $flatsData flats data
     */
    private function insertOrUpdateFlats($newbuildings, $flatsData)
    {
        $compositeFlats = [];

        $savedFlats = [];
		$savedFlatIds = [];
        foreach($newbuildings as $key => $newbuilding) {
            $savedFlats[$key] = $newbuilding->getFlats()->all();

			$current = ArrayHelper::getColumn($savedFlats[$key], 'id');
			$savedFlatIds = array_merge($savedFlatIds, $current);
        }

		$actualFlatIds = [];

        foreach($flatsData as $flatData) {
            $houseId = $flatData['houseId'];
            unset($flatData['houseId']);

            if (isset($flatData['compositeFlatId'])) {
                $compositeFlatId = $flatData['compositeFlatId'];
                unset($flatData['compositeFlatId']);
            } else {
                $compositeFlatId = NULL;
            }

            if (($flat = $this->checkFlatExists($savedFlats[$houseId], $flatData))) {
                if ((float)$flatData['unit_price_cash'] !== (float)$flat->unit_price_cash
                    || (float)$flatData['price_cash'] !== (float)$flat->price_cash
                    || (isset($flatData['unit_price_credit']) && (float)$flatData['unit_price_credit'] !== (float)$flat->unit_price_credit)
                    || (isset($flatData['price_credit']) && (float)$flatData['price_credit'] !== (float)$flat->price_credit)
                    || $flatData['status'] != $flat->status
                ) {

                    $status = $flat->status;
                    $flat->fill($flatData, ['section', 'rooms']);
                    //$flat->rooms = $flatData['rooms'];
                    $flat = \app\models\service\Flat::applyFlatPostionOnFloorLayout($flat, ['status' => $flatData['status']], $status);
                    $flat->save(false);
                    $this->updatedFlatsCount++;
                }
				else {
					$flat->touch('updated_at');
				}

				$actualFlatIds[] = $flat->id;
            } else {
                $flat = new Flat($flatData);
                $flat->newbuilding_id = $newbuildings[$houseId]->id;
                $flat->rooms = $flatData['rooms'];

                if (!is_null($compositeFlatId)) {
                    if (isset($compositeFlats[$compositeFlatId])) {
                        $compositeFlat = $compositeFlats[$compositeFlatId];
                    } else {
                        $compositeFlat = new CompositeFlat();
                        $compositeFlat->save();
                        $compositeFlats[$compositeFlatId] = $compositeFlat;
                    }

                    $flat->composite_flat_id = $compositeFlat->id;
                }

                if (isset($flatData['layout'])) {
                    $flat->layout = $this->downloadFile($flatData['layout'], $flat);
                }

                $flat->save(false);
                $this->insertedFlatsCount++;
            }

		}

		$missedFlats = array_diff($savedFlatIds, $actualFlatIds);

		$this->updateFlatStatus($missedFlats, Flat::STATUS_SOLD);

    }

	private function updateFlatStatus($flatIds, $status)
	{
		$flats = Flat::find()->where(['in', 'id', $flatIds])->all();

		foreach ($flats as $flat) {
			$flat->status = $status;
			$flat->save();
		}
	}

    /**
     * Check that floor layout with given attributes exists
     *
     * @param array $floorLayouts
     * @param array $floorLayoutData
     * @return boolean
     */
    private function checkFloorLayoutExists($floorLayouts, $floorLayoutData)
    {
        foreach($floorLayouts as $floorLayout) {
            if ($floorLayout->floor == $floorLayoutData['floor']
                && $floorLayout->section == $floorLayoutData['section']
            ) {
                return $floorLayout;
            }
        }

        return false;
    }

    /**
     * Check that flat with given attributes exists
     *
     * @param array $flats
     * @param array $flatData
     * @return boolean
     */
    private function checkFlatExists($flats, $flatData)
    {
        foreach($flats as $flat) {
            if ($flat->number == $flatData['number']
                && $flat->floor == $flatData['floor']
            ) {
                return $flat;
            }
        }

        return false;
    }

     /** Download file by url
     *
     * @param type $url url where file is enable
     * @return string saved filename (only basename)
     */
    private function downloadFile($url, $flat = null)
    {
        if (is_null($url)) {
            return NULL;
        }

        $filename = mt_rand() . '-' . basename($url);
		$url = str_replace ( ' ', '%20', $url);
		$fh = fopen(\Yii::getAlias("@webroot/uploads/$filename"), 'w');
        $client = new Client([
            'transport' => 'yii\httpclient\CurlTransport'
        ]);
		try {
			$response = $client->createRequest()
	            ->setMethod('GET')
	            ->setUrl($url)
	            ->setOutputFile($fh)
	            ->send();
		} catch (\Exception $e) {
			return NULL;
		}

        return $filename;
    }
}
