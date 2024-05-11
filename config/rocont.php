<?php
// файл с кастомными переменными окружения
    return [
        'main_mail' => env('MAIN_MAIL', false),
        'debug_mail' => env('DEBUG_MAIL', 'errors@rocont.ru'),
        'mails_disabled' => env('MAIL_DISABLED', false),
    ];
