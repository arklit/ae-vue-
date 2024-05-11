<?php

namespace App\Helpers;

class FormsConfig
{
    public static function getFormByKey($key): ?array
    {
        $config = self::getFullConfig();
        return $config[$key] ?? null;
    }

    private static function getFullConfig(): array
    {
        return [
            'order' => [
                // Конфигурация по отправке формы
                'send' => [
                    'method' => 'sendMailWithMemoryFiles',
                    'subject' => 'Заявка на бронирование',
                    'view' => 'mails.admin.business-form',
                    'key' => 'email',
                ],
                // Конфигурация по отображению формы
                'view' => [
                    'title' => '',
                    'description' => '',
                    'form_class' => 'landing-form-form',
                    'button_text' => 'Отправить',
                    'button_class' => 'book_now_btn landing-form-submit ya-form',
                ],
                // Конфигурация полей формы
                'fields' => [
                    'fields_1' => [
                        'name' => [
                            'value' => '',
                            'label' => 'Имя',
                            'label_class_name' => 'field_label_class',
                            'placeholder' => 'Заполните имя',
                            'type' => 'text',
                            'id' => 'name',
                            'field_class' => 'field_class',
                            'container_field_class' => 'field_container_class',
                            'vue_field_component' => 'inputComponent',
                            'rules' => ['required', 'max:10'],
                            'messages' => [
                                'required' => 'Обязательное поле',
                                'max' => 'Максимальная длина 10 символов'
                            ]
                        ],
                        'guests' => [
                            'value' => '',
                            'label' => 'Количество гостей',
                            'label_class_name' => 'field_label_class',
                            'placeholder' => 'Ввидете количество гостей',
                            'type' => 'number',
                            'id' => 'guests',
                            'field_class' => 'field_class',
                            'container_field_class' => 'field_container_class',
                            'vue_field_component' => 'inputComponent',
                            'rules' => ['required', 'max:10'],
                            'messages' => [
                                'required' => 'Обязательное поле',
                                'max' => 'Максимальная длина 10 символов'
                            ]
                        ]
                    ],
                    'organization' => [
                        'value' => '',
                        'label' => 'Название организации',
                        'label_class_name' => 'field_label_class',
                        'placeholder' => 'Введите название организации',
                        'type' => 'text',
                        'id' => 'organization',
                        'field_class' => 'field_class',
                        'container_field_class' => 'field_container_class',
                        'vue_field_component' => 'inputComponent',
                        'rules' => ['required'],
                        'messages' => [
                            'required' => 'Обязательное поле',
                        ]
                    ],
                    'fields_2' => [
                        'email' => [
                            'value' => '',
                            'label' => 'Почта',
                            'label_class_name' => 'field_label_class',
                            'placeholder' => 'Введите почту',
                            'type' => 'email',
                            'id' => 'email',
                            'field_class' => 'field_class',
                            'container_field_class' => 'field_container_class',
                            'vue_field_component' => 'inputComponent',
                            'rules' => ['required', 'email'],
                            'messages' => [
                                'required' => 'Обязательное поле',
                                'email' => 'Введите правильную почту'
                            ]
                        ],
                        'phone' => [
                            'value' => '',
                            'label' => 'Номер телефона',
                            'label_class_name' => 'field_label_class',
                            'placeholder' => 'Введите номер телефона',
                            'mask' => '+7 (###) ###-##-##',
                            'type' => 'text',
                            'id' => 'phone',
                            'field_class' => 'field_class',
                            'container_field_class' => 'field_container_class',
                            'vue_field_component' => 'inputComponent',
                            'rules' => ['required', 'min:18'],
                            'messages' => [
                                'required' => 'Обязательное поле',
                                'min' => 'Минимальная длина 18 символов'
                            ]
                        ],
                    ],
                    'fields_3' => [
                        'date_from' => [
                            'value' => '',
                            'label' => 'Дата начала',
                            'label_class_name' => 'field_label_class',
                            'placeholder' => '__.__.____',
                            'type' => 'text',
                            'id' => 'date_from',
                            'field_class' => 'field_class',
                            'container_field_class' => 'field_container_class',
                            'vue_field_component' => 'datePickerComponent',
                            'rules' => ['required', 'before_or_equal:date_to'],
                            'messages' => [
                                'required' => 'Обязательное поле',
                                'max' => 'Максимальная длина 10 символов',
                                'before_or_equal' => 'Дата начала должна быть равна или позже даты конца',
                            ]
                        ],
                        'date_to' => [
                            'value' => '',
                            'label' => 'Дата конца',
                            'label_class_name' => 'field_label_class',
                            'placeholder' => '__.__.____',
                            'type' => 'text',
                            'id' => 'date_to',
                            'field_class' => 'field_class',
                            'container_field_class' => 'field_container_class',
                            'vue_field_component' => 'datePickerComponent',
                            'rules' => ['required', 'after_or_equal:date_from'],
                            'messages' => [
                                'required' => 'Обязательное поле',
                                'max' => 'Максимальная длина 10 символов',
                                'after_or_equal' => 'Дата коннца должна быть позже или равна дате начала',
                            ]
                        ],
                    ],
                    'policy' => [
                        'value' => false,
                        'label' => 'Согласие с <a href="/policy">политикой конфиденциальности</a>',
                        'label_class_name' => 'field_label_class',
                        'placeholder' => '',
                        'type' => 'checkbox',
                        'id' => 'landing-policy',
                        'error_class' => 'form-error absolute',
                        'field_class' => 'field_checkbox',
                        'container_field_class' => 'field_container_class',
                        'vue_field_component' => 'inputComponent',
                        'rules' => ['accepted'],
                        'messages' => [
                            'accepted' => 'Обязательное поле'
                        ]
                    ],
                ]
            ],
        ];
    }
}
