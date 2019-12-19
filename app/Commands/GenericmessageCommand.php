<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\SystemCommands\USDCommand;
use Longman\TelegramBot\Commands\SystemCommands\RUBCommand;
use Longman\TelegramBot\Commands\SystemCommands\EURCommand;

class GenericmessageCommand extends SystemCommand
{
    protected $name = 'Genericmessage';
    protected $description = 'Handle generic message';
    protected $version = '1.0.0';

    protected const BUTTONS = [
        'USD',
        'RUB',
        'EUR',
    ];

    public function execute()
    {
        $text = trim($this->getMessage()->getText(true));

        $update = json_decode($this->update->toJson(), true);
        \Longman\TelegramBot\TelegramLog::error('test', $text);
        \Longman\TelegramBot\TelegramLog::error('test', $update);
        foreach (self::BUTTONS as $button) {
            \Longman\TelegramBot\TelegramLog::error('while');
            if ($text === $button) {
                \Longman\TelegramBot\TelegramLog::error('if');
                $update['message']['text'] = "/{$button}";
                $command = $button . 'Command';
                return (new $command($this->telegram, new Update($update)))->preExecute();
            }
        }
        return Request::emptyResponse();
    }
}