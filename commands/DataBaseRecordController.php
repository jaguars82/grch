<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use Yii;
use app\models\Flat;
use app\models\PriceChange;
use app\models\AreaChange;

class DataBaseRecordController extends Controller
{
    public function actionPriceAreaChangeInit()
    {
        $flats = Flat::find()
            ->joinWith('newbuilding')
            ->where(['flat.status' => [0, 1]])
            ->andWhere(['newbuilding.active' => 1])
            ->all();

        foreach ($flats as $flat) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Check existing entries in 'price_change'
                $existingPriceChange = PriceChange::find()
                    ->where(['flat_id' => $flat->id])
                    ->exists();

                // Create an entry
                if (!$existingPriceChange && $flat->price_cash > 0.01) {
                    $priceChange = new PriceChange([
                        'flat_id' => $flat->id,
                        'price_cash' => $flat->price_cash,
                        'unit_price_cash' => $flat->unit_price_cash,
                        'price_credit' => $flat->price_credit,
                        'unit_price_credit' => $flat->unit_price_credit,
                        'area_snapshot' => $flat->area,
                        'is_initial' => 1,
                    ]);
                    $priceChange->save(false);
                }

                // Check existing entries in 'area_change'
                $existingAreaChange = AreaChange::find()
                    ->where(['flat_id' => $flat->id])
                    ->exists();

                // Create an entry
                if (!$existingAreaChange && $flat->area > 0.01) {
                    $areaChange = new AreaChange([
                        'flat_id' => $flat->id,
                        'area' => $flat->area,
                        'is_initial' => 1,
                    ]);
                    $areaChange->save(false);
                }

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                echo "Ошибка при обработке квартиры ID {$flat->id}: " . $e->getMessage() . "\n";
            }
        }

        echo "Инициализационные записи успешно созданы.\n";
        return ExitCode::OK;
    }
}