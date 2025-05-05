<?php

namespace app\commands;

use yii\console\Controller;
use yii\helpers\ArrayHelper;
use app\models\Newbuilding;
use app\models\Entrance;
use app\models\Flat;
use app\models\User;
use app\models\SecondaryAdvertisement;

class UtilsController extends Controller
{
    /**
     * Action to fill 'entrance_id' field in 'flat' table
     * according to the value of its 'section' field
     * It also adds a new record to 'entrance' table if needed
     */
    public function actionFetchEntrances()
    {
        $Newbuildings = (new Newbuilding())->find()->all();

        foreach($Newbuildings as $Newbuilding) {

            $NewbuildingEntrances = array();

            foreach($Newbuilding->flats as $flat) {
                
                // try to find entrance in database
                if(empty($flat->entrance_id) && !empty($flat->section)) {
                    $entrance = (new Entrance())
                    ->find()
                    ->where([ 'newbuilding_id' => $flat->newbuilding_id ])
                    ->andWhere([ 'number' => $flat->section ])
                    ->one();

                    if($entrance instanceof Entrance) { // if entrance exists: save its ID in 'entrance_id' field for current flat
                        $flat->entrance_id = $entrance->id;
                        $flat->save();
                    } else { // if entrance doesn't exist: add a new record to 'entrance' table and copy some data from 'newbuilding' table to 'entrance' table
                        $entrance = new Entrance();
                        $entrance->newbuilding_id = $Newbuilding->id;
                        $entrance->name = 'Подъезд '.$flat->section;
                        $entrance->number = $flat->section;
                        $entrance->floors = $Newbuilding->total_floor;
                        $entrance->material = $Newbuilding->material;
                        $entrance->status = $Newbuilding->status;
                        $entrance->deadline = $Newbuilding->deadline;
                        $entrance->save();
                        // save entrance's ID in 'entrance_id' field for current flat
                        $flat->entrance_id = $entrance->id;
                        $flat->save();
                    }
                }
            }
        }
    }


    /**
     * Action to fill 'index_on_floor' field in 'flat' table
     * according to flat's position on the floor
     * 
     * var $newbuildingId - may specify a particular building to fetch indexes in
     * var $rebuildIndexes - rewrite existing indexes (if true), fill only empty indexes (if false)
     */
    public function actionFetchFlatIndexes($newbuildingId = 0, $rebuildIndexes = false)
    {
        if ($newbuildingId === 0) {
            $entrances = Entrance::find()->all();
        } else {
            $building = Newbuilding::findOne($newbuildingId);
            $entrances = $building->entrances;
        }

        foreach ($entrances as $entrance) {
            $flats = Flat::find()
                ->where(['entrance_id' => $entrance->id])
                ->all();

            $flatsGrouppedByFloor = array();

            foreach ($flats as $flat) {
                $flatsGrouppedByFloor[$flat->floor][] = $flat;
            }

            foreach ($flatsGrouppedByFloor as $floor => $floorFlats) {
                $flatIndex = 1;
                foreach ($floorFlats as $flat) {
                    if (
                        ($rebuildIndexes === true && $flat->index_on_floor != $flatIndex)
                        || ($rebuildIndexes === false && empty($flat->index_on_floor))
                    ) {
                        $flat->index_on_floor = $flatIndex;
                        $flat->save();
                    }
                    $flatIndex++;
                }
            }
        }
    }


    /**
     * Clear user's phone numbers from any symbols except digits and '+'
     */
    public function actionNormalizeUserPhones ()
    {
        $users = User::find()->all();

        $transaction = \Yii::$app->db->beginTransaction();

        try {
            foreach ($users as $user) {
                if (!empty($user->phone)) {
                    $user->phone = preg_replace("/[^0-9+]/", '', $user->phone);
                    $user->save(false);
                }
            }
            
            $transaction->commit(); 
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo "Ошибка: " . $e->getMessage() . "\n";
        }
    }


    /**
     * Tries to find the author of a secondary advertisement by email or phone and (if found) attach his Id to the advertisement
     */
    public function actionFetchAdvertisementsAuthors ($agency = null)
    {
        $query = SecondaryAdvertisement::find()->where(['author_id' => NULL]);
        
        if(!is_null($agency)) {
            $query->andWhere(['agency_id' => $agency]);
        }

        $advertisements = $query->all();

        if (is_null($advertisements)) return;

        foreach ($advertisements as $advertisement) {
            $author_id = null;
            $authorInfo = ArrayHelper::toArray(json_decode($advertisement->author_info));

            // Try by email
            if (array_key_exists('email', $authorInfo) && !empty($authorInfo['email'])) {
                if (!is_null($agency)) {
                    $authorByMailTry = User::findByEmailAndAgency($authorInfo['email'], $agency);
                } else {
                    $authorByMailTry = User::findByEmail($authorInfo['email']);
                }
                if (!empty($authorByMailTry)) {
                    $author_id = $authorByMailTry->id;
                }
            }

            // Try by phone
            if (is_null($author_id) && array_key_exists('phones', $authorInfo) && !empty($authorInfo['phones'])) {
                foreach ($authorInfo['phones'] as $phone) {
                    if (!is_null($agency)) {
                        $authorByPhoneTry = User::findByPhoneAndAgency($phone, $agency);
                    } else {
                        $authorByPhoneTry = User::findByPhone($phone);
                    }
                    if (!empty($authorByPhoneTry)) {
                        $author_id = $authorByPhoneTry->id;
                        break;
                    }
                }
            }

            if (!is_null($author_id)) {
                $advertisement->author_id = $author_id;
                $advertisement->save(false);
            }
        }
    }


    /**
     * command to generate password hash from given password
     */
    public function actionMakePassHash ($pass)
    {
        $hash = \Yii::$app->security->generatePasswordHash($pass);
        echo $hash;
    }
}