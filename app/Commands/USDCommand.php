<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;
use USD2UAH\Currency\MinfinApi;

/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 */
class USDCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'USD';
    /**
     * @var string
     */
    protected $description = 'USD command';
    /**
     * @var string
     */
    protected $usage = '/USD';
    /**
     * @var string
     */
    protected $version = '1.1.0';
    /**
     * @var bool
     */
    protected $private_only = true;
    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $exchange = (new MinfinApi())->getCurrencyList();
        $text    = "
**Курс USD к UAH**
**Межбанк**
Покупка: {$exchange[MinfinApi::MB]['usd']['bid']} 
Продажа: {$exchange[MinfinApi::MB]['usd']['ask']} 

**НБУ**
Покупка: {$exchange[MinfinApi::NBU]['usd']['bid']} 
Продажа: {$exchange[MinfinApi::NBU]['usd']['ask']} 

**Средний курс в банках**
Покупка: {$exchange[MinfinApi::BANKS]['usd']['bid']} 
Продажа: {$exchange[MinfinApi::BANKS]['usd']['ask']} 

Курс валют предоставлен: [Минфин](https://minfin.com.ua/currency/?utm_source=telegram&utm_medium=USD2UAH_bot&utm_compaign=usd_post)";
        $data = [
            'chat_id' => $chat_id,
            'text'    => $text,
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
        ];
        return Request::sendMessage($data);
    }
}