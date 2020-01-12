<?php

use CurrencyUaBot\Cache\RedisStorage;
use CurrencyUaBot\Core\App;
use CurrencyUaBot\Core\Connection;
use Monolog\Logger;

require __DIR__ . '/vendor/autoload.php';
$env = Dotenv\Dotenv::createImmutable(__DIR__);
$env->load();
$commands_paths = [
    __DIR__ . '/app/Commands/',
];

$token  = getenv('TG_TOKEN');
$botName  = getenv('TG_BOT_NAME');

try {

    App::bind('log_path', __DIR__ . '/logs/');
    $logger = new Monolog\Logger('app');
    $logger->pushHandler(new  Monolog\Handler\StreamHandler(App::get('log_path') . 'app.log', Logger::ERROR));
    $logger->pushHandler(new  Monolog\Handler\StreamHandler(App::get('log_path') . 'debug.log', Logger::DEBUG));
    App::bind('logger', $logger);

    App::bind('db', Connection::get());

    App::bind('redis', RedisStorage::getInstance());

    $telegram = new Longman\TelegramBot\Telegram($token, $botName);
    (new CurrencyUaBot\Helpers\BotRegistrator($telegram))->register($botName);

    Longman\TelegramBot\TelegramLog::initialize($logger);

    $telegram->enableAdmin(intval(getenv('ADMIN')));
    $telegram->addCommandsPaths($commands_paths);

    $telegram->enableExternalMySql(App::get('db'));

    $telegram->enableLimiter();
    $telegram->handle();

}  catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Log telegram errors
    Longman\TelegramBot\TelegramLog::error($e);
} catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
    // Catch log initialisation errors
    $logger->error($e->getMessage());
} catch (Exception $e) {
    $logger->error($e->getMessage());
}

echo json_encode(['check' => true]);
