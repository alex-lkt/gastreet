<?php
define('SMSRU_API_KEY', 'AC7B435B-E804-3887-4E5B-06745A3F578B');
define('FNS_API_KEY', '...'); // API ключ ФНС
define('OK_REGISTER_LINK', '/account/'); // Редирект после регистрации пользователя
define('OK_AUTH_LINK', '/account/'); // Редирект после авторизации пользователя
define('SMS_CODE_LIFETIME', '60'); // Время жизни СМС кода в мин.

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

firstbit\Events::RegisterEvents();
