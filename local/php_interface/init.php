<?php
define('SMSRU_API_KEY', 'AC7B435B-E804-3887-4E5B-06745A3F578B');
define('DADATA_TOKEN', '130f8f12a29f4f7ca990ad4be6fe1f1e5bc497c1'); // Токен dadata.ru
define('DADATA_SECRET', 'a38db9106d09f2cfef9644087f883bbbcd2d805c'); // Secret dadata.ru
define('OK_REGISTER_LINK', '/account/'); // Редирект после регистрации пользователя
define('OK_AUTH_LINK', '/account/'); // Редирект после авторизации пользователя
define('SMS_CODE_LIFETIME', '60'); // Время жизни СМС кода в мин.

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

firstbit\Events::RegisterEvents();
