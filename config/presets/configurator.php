<?php

use App\Orchid\Fields\Cropper;
use Orchid\Screen\Fields\Code;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;

return [
    [
        'title' => 'Настройки',
        'group' => [
            'count' => 2,
            'fields' => [
                'address' => [
                    'title' => 'Адрес',
                    'type' => Input::class,
                    'required' => true,
                ],
                'schedule' => [
                    'title' => 'График работы',
                    'type' => TextArea::class,
                    'required' => true,
                ],
                'phone' => [
                    'title' => 'Номер телефона',
                    'type' => Input::class,
                    'mask' => '+7 (###) ###-####',
                    'required' => true,
                ],
                'email_contacts' => [
                    'title' => 'E-mail для связи',
                    'type' => Input::class,
                    'required' => true,
                ],
                'email_forms' => [
                    'title' => 'E-mail для получения заявок из форм',
                    'type' => Input::class,
                    'required' => true,
                ],
                'email_orders' => [
                    'title' => 'E-mail для получения заказов',
                    'type' => Input::class,
                    'required' => true,
                ],
                'coordinates' => [
                    'title' => 'Координаты',
                    'type' => Input::class,
                    'required' => true,
                ],
                'tg' => [
                    'title' => 'Ссылка на Telegram',
                    'type' => Input::class,
                    'required' => true,
                ],
                'wp' => [
                    'title' => 'Ссылка на WhatsApp',
                    'type' => Input::class,
                    'required' => true,
                ],
            ],
        ],
    ],
    [
        'title' => 'Скрипты',
        'group' => [
            'count' => 2,
            'fields' => [
                'head' => [
                    'title' => 'Скрипты в head',
                    'type' => Code::class,
                    'required' => false,
                ],
                'body_start' => [
                    'title' => 'Скрипты в начало body',
                    'type' => Code::class,
                    'required' => false,
                ],
                'body_end' => [
                    'title' => 'Скрипты в конец body',
                    'type' => Code::class,
                    'required' => false,
                ]
            ],
        ],
    ],
];
