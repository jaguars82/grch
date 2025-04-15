<?php

namespace app\services;

use yii\httpclient\Client;

class TelegramSender
{
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

    public function sendMessage($chatId, $text)
    {
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
        
        $response = $this->httpClient->createRequest()
            ->setMethod('POST')
            ->setUrl($url)
            ->setData([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ])
            ->send();

        if ($response->isOk) {
            return true;
        } else {
            \Yii::error("Telegram API error: " . $response->content);
            return false;
        }
    }
}