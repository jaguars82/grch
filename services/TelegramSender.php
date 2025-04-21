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
        [
            'name' => 'ГРЧ (информация)',
            'id' => -1002573609179,
            'has_threads' => false,
        ],
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
            ]
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

    public function sendToAllGroups($message, int $dealType, int $categoryId)
    {
        foreach (self::$telegram_groups as $group) {
            $chatId = $group['id'];
            $threadId = false;

            if (!empty($group['has_threads']) && !empty($group['threads']) && is_array($group['threads'])) {
                $threadKey = $this->defineThreadId($dealType, $categoryId);
                $threadId = $group['threads'][$threadKey] ?? ($group['threads'][self::THREAD_DEFAULT] ?? false);
            }

            $this->sendMessage($chatId, $message, $threadId);
        }
    }
}
