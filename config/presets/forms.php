<?php

return [
    'order' => [
        // Конфигурация по отправке формы
        'send' => [
            'method' => 'sendMailWithMemoryFiles',
            'subject' => 'Форма заказа',
            'view' => 'mails.admin.order-form',
            'key' => 'email',
        ],
        // Конфигурация по отображению формы
        'view' => [
            'title' => 'Заголовок формы',
            'description' => 'Описание формы',
            'form_class' => 'form',
            'button_text' => 'text',
            'button_class' => 'btn',
        ],
        // Конфигурация полей формы
        'fields' => [
            'name' => [
                'value' => '',
                'label' => 'Имя',
                'placeholder' => 'Введите имя',
                'type' => 'text',
                'id' => 'name',
                'field_class' => 'field_input',
                'container_field_class' => 'field_container',
                'vue_field_component' => 'inputComponent',
                'rules' => ['required','max:10'],
                'messages' => [
                    'required' => 'Это поле обязательно для заполнения',
                    'max' => 'Максимальная длина 10 символов'
                ]
            ],
            'date' => [
                'value' => '',
                'label' => 'Дата',
                'placeholder' => '__.__.____',
                'type' => 'text',
                'id' => 'date',
                'field_class' => 'field_date',
                'container_field_class' => 'field_container',
                'vue_field_component' => 'datePickerComponent',
                'rules' => ['required'],
                'messages' => [
                    'required' => 'Это поле обязательно для заполнения',
                    'max' => 'Максимальная длина 10 символов'
                ]
            ],
            'email' => [
                'value' => '',
                'label' => 'Почта',
                'placeholder' => 'Введите почту',
                'type' => 'email',
                'id' => 'email',
                'field_class' => 'field_input',
                'container_field_class' => 'field_container',
                'vue_field_component' => 'inputComponent',
                'rules' => ['required', 'email'],
                'messages' => [
                    'required' => 'Это поле обязательно для заполнения',
                    'email' => 'Это поле должно содержать почту'
                ]
            ],
            'phone' => [
                'value' => '',
                'label' => 'Телефон',
                'placeholder' => 'Введите номер телефона',
                'mask' => '+7 (###) ###-##-##',
                'type' => 'text',
                'id' => 'phone',
                'field_class' => 'field_input',
                'container_field_class' => 'field_container',
                'vue_field_component' => 'inputComponent',
                'rules' => ['required', 'min:18'],
                'messages' => [
                    'required' => 'Это поле обязательно для заполнения',
                    'min' => 'Введите полный номер телефона'
                ]
            ],
            'select' => [
                'value' => '123123',
                'label' => 'Селект',
                'placeholder' => 'Выберите значение',
                'type' => 'select',
                'id' => 'select',
                'field_class' => 'field_select',
                'container_field_class' => 'field_container',
                'vue_field_component' => 'selectComponent',
                'rules' => ['max:10'],
                'messages' => [
                    'max' => 'Максимальная длина 10 символов'
                ]
            ],
            'checkbox' => [
                'value' => false,
                'label' => 'Согласие с <a href="/policy">политикой конфиденциальности</a>',
                'placeholder' => '',
                'type' => 'checkbox',
                'id' => 'checkbox',
                'field_class' => 'field_checkbox',
                'container_field_class' => 'field_container checkbox',
                'vue_field_component' => 'inputComponent',
                'rules' => ['accepted'],
                'messages' => [
                    'accepted' => 'Это поле обязательно для заполнения'
                ]
            ],
            'fields_1' => [
                'surname' => [
                    'value' => '',
                    'label' => 'Фамилия',
                    'placeholder' => 'Фамилия',
                    'type' => 'text',
                    'id' => 'surname',
                    'field_class' => 'field_input',
                    'container_field_class' => 'field_container',
                    'vue_field_component' => 'inputComponent',
                    'rules' => ['required'],
                    'messages' => [
                        'required' => 'Это поле обязательно для заполнения'
                    ]
                ],
                'docs' => [
                    'value' => '',
                    'label' => 'Документы',
                    'placeholder' => '',
                    'type' => 'text',
                    'id' => 'docs',
                    'accept' => 'application/pdf,application/vnd.ms-excel',
                    'field_class' => 'field_file',
                    'container_field_class' => 'field_container',
                    'vue_field_component' => 'fileComponent',
                    'multiple' => true,
                    'rules' => ['required'],
                    'messages' => [
                        'required' => 'Это поле обязательно для заполнения'
                    ]
                ],
            ],
        ]
    ],
];
