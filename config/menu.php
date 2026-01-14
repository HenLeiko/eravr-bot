<?php

return [
    'main' => [
        'title' => 'Главное меню',
        'options' => [
            'shifts' => [
                'title' => 'Смены',
                'options' => [
                    'open_belyaevo' => ['title' => 'В клубе Беляево'],
                    'open_molodega' => ['title' => 'В клубе Молодёжная'],
                    'open_selega' => ['title' => 'В клубе Поносово'],
                    'back' => ['title' => 'Назад']

                ]
            ],
            'smm' => [
                'title' => 'Сертификаты/приглашения',
                'options' => [
                    'create_cert' => ['title' => 'Создать сертификат'],
                    'create_invite' => ['title' => 'Создать приглашение'],
                    'back' => ['title' => 'Назад']
                ]
            ],
            'admins' => [
                'title' => 'Работа с админами',
                'options' => [
                    'count_events_by_admins' => ['title' => 'Подсчёт записей админов'],
                    'back' => ['title' => 'Назад']
                ]
            ],
            'settings' => [
                'title' => 'Настройки',
                'options' => [
                    'add_admin' => ['title' => 'Добавить админа'],
                    'rem_admin' => ['title' => 'Удалить админа'],
                    'change_level' => ['title' => 'Изменить доступы'],
                    'back' => ['title' => 'Назад']
                ]
            ]
        ]
    ],
];
