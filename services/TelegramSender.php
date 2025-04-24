<?php

namespace app\services;

use app\models\SecondaryAdvertisement;
use app\models\SecondaryCategory;
use yii\httpclient\Client;

class TelegramSender
{
    const THREAD_DEFAULT = 0;
    const THREAD_FLAT_SELL = 1;
    const THREAD_HOUSE_SELL = 2;
    const THREAD_PLOT_SELL = 3;
    const THREAD_FLAT_HOUSE_RENT = 4;
    const THREAD_COMMERCIAL_SELL_RENT = 5;

    public static $telegram_groups = [
        /*[
            'name' => 'ГРЧ (информация)',
            'id' => -1002573609179,
            'has_threads' => false,
            'templates' => [
                'message_body' => [
                    self::THREAD_DEFAULT => <<<TPL
                    В разделе <a href="https://grch.ru/secondary/index">"Вторичка"</a> для объявления <a href="{ad_url}">#{ad_id}</a> установлен статус <b>"{status_name}"</b>{expiration_date}
                    
                    <u>Информация об объявлении</u>:
                    {deal_type}{rooms_info}
                    
                    <a href="{ad_url}">Перейти к объявлению</a>
                    TPL,
                ],
                'secondary_room_item' => [
                    self::THREAD_DEFAULT => <<<TPL
                    , {category}, комнат: <b>{rooms}</b>, площадь: <b>{area}</b> м², стоимость: <b>{price}</b> ₽"
                    TPL,
                ],
            ],
        ],*/
        [
            'name' => 'ГРЧ (нет в рекламе)',
            'id' => -1002445243997,
            'has_threads' => true,
            'threads' => [
                self::THREAD_DEFAULT => 124,
                self::THREAD_FLAT_SELL => 22,
                self::THREAD_HOUSE_SELL => 23,
                self::THREAD_PLOT_SELL => 25,
                self::THREAD_FLAT_HOUSE_RENT => 26,
                self::THREAD_COMMERCIAL_SELL_RENT => 24,
                self::THREAD_DEFAULT => 27,
            ],
            'templates' => [
                'message_body' => [
                    self::THREAD_DEFAULT => '',
                    self::THREAD_FLAT_SELL => <<<TPL
                        {rooms_info}    

                        Контакт: {author_name}, {author_phone}, {author_email}
                        Дата размещения: {created} г.

                        <a href="{ad_url}">Перейти к объявлению на сайте ГРЧ</a>
                        TPL,
                    self::THREAD_HOUSE_SELL => <<<TPL
                        {rooms_info}    

                        Контакт: {author_name}, {author_phone}, {author_email}
                        Дата размещения: {created} г.

                        <a href="{ad_url}">Перейти к объявлению на сайте ГРЧ</a>
                        TPL,
                    self::THREAD_PLOT_SELL => <<<TPL
                        {rooms_info}    

                        Контакт: {author_name}, {author_phone}, {author_email}
                        Дата размещения: {created} г.

                        <a href="{ad_url}">Перейти к объявлению на сайте ГРЧ</a>
                        TPL,
                    self::THREAD_FLAT_HOUSE_RENT => <<<TPL
                        {rooms_info}    

                        Контакт: {author_name}, {author_phone}, {author_email}
                        Дата размещения: {created} г.

                        <a href="{ad_url}">Перейти к объявлению на сайте ГРЧ</a>
                        TPL,
                    self::THREAD_COMMERCIAL_SELL_RENT => <<<TPL
                        {rooms_info}    

                        Контакт: {author_name}, {author_phone}, {author_email}
                        Дата размещения: {created} г.

                        <a href="{ad_url}">Перейти к объявлению на сайте ГРЧ</a>
                        TPL,
                    self::THREAD_DEFAULT => <<<TPL
                        {rooms_info}    

                        Контакт: {author_name}, {author_phone}, {author_email}
                        Дата размещения: {created} г.

                        <a href="{ad_url}">Перейти к объявлению на сайте ГРЧ</a>
                        TPL,
                ],
                'secondary_room_item' => [
                    self::THREAD_DEFAULT => '',
                    self::THREAD_FLAT_SELL => <<<TPL
                        #{rooms}_{district_code} 
                        АДРЕС: {address_string}
                        ПЛОЩАДЬ: {area}
                        ЭТАЖ: {floor}
                        ЦЕНА: {price}
                        
                        Краткое описание: {detail}
                        TPL,
                    self::THREAD_HOUSE_SELL => <<<TPL
                        #{district_code} 
                        АДРЕС: {address_string}
                        ПЛОЩАДЬ: {area}
                        ЦЕНА: {price}
                        
                        Краткое описание: {detail}
                        TPL,
                    self::THREAD_PLOT_SELL => <<<TPL
                        #{district_code} 
                        АДРЕС: {address_string}
                        ПЛОЩАДЬ: {area}
                        ЦЕНА: {price}
                        
                        Краткое описание: {detail}
                        TPL,
                    self::THREAD_FLAT_HOUSE_RENT => <<<TPL
                        #{rooms}_{district_code} 
                        АДРЕС: {address_string}
                        ПЛОЩАДЬ: {area}
                        ЭТАЖ: {floor}
                        ЦЕНА: {price}
                        
                        Краткое описание: {detail}
                        TPL,
                    self::THREAD_COMMERCIAL_SELL_RENT => <<<TPL
                        #{deal_code}_{district_code} 
                        АДРЕС: {address_string}
                        ПЛОЩАДЬ: {area}
                        ЭТАЖ: {floor}
                        ЦЕНА: {price}
                        
                        Краткое описание: {detail}
                        TPL,
                    self::THREAD_DEFAULT => <<<TPL
                        #{rooms}_{district_code} 
                        АДРЕС: {address_string}
                        ПЛОЩАДЬ: {area}
                        ЭТАЖ: {floor}
                        ЦЕНА: {price}
                        
                        Краткое описание: {detail}
                        TPL,
                ]
            ],
        ],
    ];

    public static function create($botToken)
    {
        return new self($botToken);
    }

    private $botToken;
    private $httpClient;

    private function __construct($botToken)
    {
        $this->botToken = $botToken;
        $this->httpClient = new Client();
    }

    /**
     * Fill named variables in the template with corresponding values
     * 
     * @param string $template - message template with variables in {var_name} format
     * @param array $variables - array with variables [var_name => value]
     * @return string - the body of a message
     */
    public function renderTemplate($template, array $variables)
    {
        // Replacing all the {key} with corresponding values
        return preg_replace_callback('/\{(\w+)\}/', function($matches) use ($variables) {
            $key = $matches[1];
            return $variables[$key] ?? $matches[0]; // if there is no corresponding value - leave the template as is
        }, $template);
    }

    /**
     * Compose the body of the message
     * 
     * @param int $tempateId The id templates in self::$telegram_groups
     * @param array $advertisementData 
     * @param array $statusData 
     * @return string
     */
    public function buildAdvertisementMessage(int $groupIndex, int $templateId, array $advertisementData, array $statusData)
    {
        // Compose info for secondary rooms
        $roomsInfo = '';
        foreach ($advertisementData['rooms'] as $room) {
            $roomsInfo .= $this->renderTemplate(self::$telegram_groups[$groupIndex]['templates']['secondary_room_item'][$templateId], [
                'deal_code' => $room['deal_code'] ? $room['deal_code'] : '',
                'category' => $room['category_name'],
                'rooms' => $room['rooms'] ? $room['rooms'] : '',
                'area' => $room['area'] ? $room['area'] : '',
                'price' => $room['price'] ? $room['price'] : '',
                'floor' => $room['floor'] ? $room['floor'] : '',
                'detail' => $room['detail'] ? $room['detail'] : '',
                'district_code' => $room['district_code'] ? $room['district_code'] : '',
                'address_string' => $room['address_string'] ? $room['address_string'] : '',
            ]);
        }

        // Gether all the variables
        $variables = [
            'ad_url' => 'https://grch.ru/secondary/view?id='.$advertisementData['id'],
            'ad_id' => $advertisementData['id'],
            'status_name' => $statusData['name'],
            'expiration_date' => $statusData['has_expiration'] ? " (до <b>".date('d.m.Y', strtotime($statusData['expires_at']))."</b> г. включительно)" : '',
            'deal_type' => $advertisementData['deal_type'],
            'created' => $advertisementData['created'],
            'author_name' => $advertisementData['author_name'],
            'author_email' => $advertisementData['author_email'],
            'author_phone' => $advertisementData['author_phone'],
            'rooms_info' => $roomsInfo
        ];

        return $this->renderTemplate(self::$telegram_groups[$groupIndex]['templates']['message_body'][$templateId], $variables);
    }

    /**
     * Send a message to a specified Telegram chat/thread
     */
    public function sendMessage($chatId, $text, $threadId = false)
    {
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ];

        if ($threadId !== false) {
            $data['message_thread_id'] = $threadId;
        }

        $response = $this->httpClient->createRequest()
            ->setMethod('POST')
            ->setUrl($url)
            ->setData($data)
            ->send();

        if ($response->isOk) {
            return true;
        } else {
            \Yii::error("Telegram API error: " . $response->content);
            return false;
        }
    }

    public function defineThreadId(int $dealType, int $categoryId)
    {
        if ($dealType === SecondaryAdvertisement::DEAL_TYPE_SELL) {
            switch ($categoryId) {
                case SecondaryCategory::ROOT_CATEGORY_FLAT:
                    return self::THREAD_FLAT_SELL;
                case SecondaryCategory::ROOT_CATEGORY_HOUSE:
                    return self::THREAD_HOUSE_SELL;
                case SecondaryCategory::ROOT_CATEGORY_PLOT:
                    return self::THREAD_PLOT_SELL;
                case SecondaryCategory::ROOT_CATEGORY_COMMERCIAL:
                    return self::THREAD_COMMERCIAL_SELL_RENT;
            }
        } elseif ($dealType === SecondaryAdvertisement::DEAL_TYPE_RENT) {
            if (in_array($categoryId, [SecondaryCategory::ROOT_CATEGORY_FLAT, SecondaryCategory::ROOT_CATEGORY_HOUSE])) {
                return self::THREAD_FLAT_HOUSE_RENT;
            } elseif ($categoryId === SecondaryCategory::ROOT_CATEGORY_COMMERCIAL) {
                return self::THREAD_COMMERCIAL_SELL_RENT;
            }
        }

        return self::THREAD_DEFAULT;
    }

    public function sendToAllGroups(array $advertisementData, array $statusData, int $dealType, int $categoryId)
    {
        foreach (self::$telegram_groups as $key => $group) {
            $chatId = $group['id'];
            $threadId = false;

            $threadKey = $this->defineThreadId($dealType, $categoryId);

            if (!empty($group['has_threads']) && !empty($group['threads']) && is_array($group['threads'])) {
                $threadId = $group['threads'][$threadKey] ?? ($group['threads'][self::THREAD_DEFAULT] ?? false);
            }

            $templateKey = array_key_exists($threadKey, $group['templates']['message_body']) && array_key_exists($threadKey, $group['templates']['secondary_room_item']) ? $threadKey : self::THREAD_DEFAULT;

            $message = $this->buildAdvertisementMessage($key, $templateKey, $advertisementData, $statusData);

            $this->sendMessage($chatId, $message, $threadId);
        }
    }
}
