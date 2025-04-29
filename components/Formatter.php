<?php

namespace app\components;

use yii\i18n\Formatter as BaseFormatter;
use yii\helpers\Html;

/**
 * Advanced version of formatter
 */
class Formatter extends BaseFormatter
{  
    /**
     * Transform given quarter and year to date
     * 
     * @param integer $quarter
     * @param integer $year
     * @return type
     */
    public function asDateFromQuarterAndYear($quarter, $year)
    {
        $month = [
            1 => 1,
            2 => 4,
            3 => 7,
            4 => 0,
        ];
        
        return "{$year}-{$month[$quarter]}-01 00:00:01";
    }
    
    /**
     * Transform given date string to string contains quarter and year values
     * 
     * @param string $value date string
     * @param boolean $isQuarterString is add quarter value in result
     * @return string
     */
    public function asQuarterAndYearDate($value, $isQuarterString = true)
    {
        $date = new \DateTime($value);
        
        $quarter = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
        ];
        
        return $quarter[(int)ceil($date->format('m') / 3)] 
                . ($isQuarterString ? ' квартал ' : ' ') 
                . $date->format('Y');
    }

    /**
     * Transform given date string to human-readable form
     *
     * @param string $date
     * @return string
     */
    public function asUpdateDate($date)
    {
        return date('d.m.Y в H:i', strtotime($date));
    }
    
    /**
     * Transform given value to string contains current floor and all floors values
     * 
     * @param int $floor current floor
     * @param int $totalFloor all floors count
     * @return string
     */
    public function asFloor($floor, $totalFloor)
    {
        return "$floor/$totalFloor";
    }
    
    /**
     * Transform given value to string contains district value
     * 
     * @param int $district
     * @return string
     */
    public function asDistrict($district)
    {
        return "р-н " . mb_strtoupper(mb_substr($district, 0, 1)) . mb_substr($district, 1);
    }
    
    /**
     * Transform given value to string contains area value
     * 
     * @param float $area
     * @return string
     */
    public function asArea($area)
    {
        $formatted_area = number_format($area, 2, '.', "");
		// return "$formatted_area кв.м.";
		return "$formatted_area м²";
    }
    
    /**
     * Transform given value to string contains price per area value
     * 
     * @param float $price
     * @return string
     */
    public function asPricePerArea($price)
    {
        return  \Yii::$app->formatter->asDecimal($price) . ' ₽/ м²';
    }

    public function asAreaRange($minArea, $maxArea)
    {
        return $minArea . ' - ' . $this->asArea($maxArea);
    }

    /**
     * Transform given values to string contains price range
     * 
     * @param float $minPrice minimum price
     * @param float $maxPrice maximum price
     * @return string
     */
    public function asCurrencyRange($minPrice, $maxPrice, $currency = false)
    {
        return \Yii::$app->formatter->asDecimal($minPrice) 
                . " - " 
                . ($currency ? \Yii::$app->formatter->asDecimal($maxPrice) . " {$currency}" : \Yii::$app->formatter->asCurrency($maxPrice));
    }

    /**
     * Transform given value to string contains number rooms value
     * 
     * @param int $rooms
     * @return string
     */
    public function asNumberString($rooms)
    {
        return \MessageFormatter::formatMessage('be'
            ,' {rooms, plural, =1{Одно} =2{Двух} =3{Трех} =4{Четырех} =5{Пяти} other{Много}}' 
            ,['rooms' => $rooms]); 
    }

    /**
     * Transform given value to string contains number rooms value for euro flat 
     * 
     * @param int $rooms
     * @return string
     */
    public function asNumberStringEuro($rooms)
    {
        if($rooms < 4) {
            return \MessageFormatter::formatMessage('be'
                ,' {rooms, plural, =1{Студия} =2{Евродвушка} =3{Евротрешка} other{}}'
                ,['rooms' => $rooms]); 
        } else {
            return $this->asNumberString($rooms);
        }
    }
    
    /**
     * Transform given string to short string
     * 
     * @param string $text given string
     * @param int $textLength text length
     * @return string
     */
    public function asShortText($text, $textLength = 200, $isMb = false)
    {
        if ($isMb) {
            return (mb_strlen($text) <= $textLength) ? $text : mb_substr($text, 0, $textLength) . '...';
        } else {
            return (strlen($text) <= $textLength) ? $text : substr($text, 0, $textLength) . '...';
        }
    }
    
    /**
     * Transform given string to uppercase string
     * 
     * @param string $text given string
     * @return string
     */
    public function asCapitalize($text)
    {
        return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
    }
    
    /**
     * Transform given value to string contains phone value
     * 
     * @param string $phone
     * @return string
     */
    public function asPhoneLink($phone)
    {
        return !is_null($phone) && !empty($phone) ? Html::a($phone, "tel:$phone") : '<span class="not-set">(не задано)</span>';
    }
    
    /**
     * Transform worktime string to array contains worktime values
     * 
     * @param string $worktimeString worktime string
     * @return string
     */
    public function asWorktimeArray($worktimeString)
    {
        $result = [];
        
        foreach (explode(',', $worktimeString) as $day) {
            list($weekday, $from_time, $to_time) = explode('-', $day);
            if ($weekday == 'mon') {
                $weekdayString = 'Понедельник';
            } else if ($weekday == 'tue') {
                $weekdayString = 'Вторник';
            } else if ($weekday == 'wed') {
                $weekdayString = 'Среда';
            } else if ($weekday == 'thu') {
                $weekdayString = 'Четверг';
            } else if ($weekday == 'fri') {
                $weekdayString = 'Пятница';
            } else if ($weekday == 'sat') {
                $weekdayString = 'Суббота';
            } else if ($weekday == 'sun') {
                $weekdayString = 'Воскресенье';
            }
            $result[$weekdayString] = ['from' => $from_time, 'to' => $to_time];
        }
        
        return $result;
    }
    
    /**
     * Transform given string to string contains rooms value
     * 
     * @param string $string given string
     * @return string
     */
    public function asRooms($string)
    {
        return "$string к.";
    }
    
    /**
     * Transform given string to string contains month value
     * 
     * @param string $string given string
     * @return string
     */
    public function asMonth($string)
    {
        return "$string мес.";
    }

    /**
     * Return domain from given url
     * 
     * @param string $url string url
     * @return string
     */
    public function asHost($url)
    {
        return parse_url($url, PHP_URL_HOST);
    }

    /**
     * Transform given string to string contains file size value
     */
    public function asFileSize($size, $convert = true)
    {
        if($convert) {
            $size = round($size / 1000, 1);
        }
        return $size .' Мб.';
    }

    /**
     * Transform the given string into a shorten plain text
     */
    public function asPlainShortenText(string $text, int $maxLength = 255)
    {
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim(preg_replace('/\s+/', ' ', $text));
        $text = preg_replace('/[^\p{L}\p{N}\p{P}\p{Zs}]+/u', '', $text); // remove all 'garbage' symbols

        if (mb_strlen($text) > $maxLength) {
            $text = mb_substr($text, 0, mb_strrpos(mb_substr($text, 0, $maxLength), ' ')) ?: mb_substr($text, 0, $maxLength);
            $text .= '…';
        }

        return $text;
    }
}