<?php

return [
    'enabled' => [
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'yasmartcaptcha_main',
    ],
    'service_js' => [
        'xtype' => 'textfield',
        'value' => 'https://smartcaptcha.yandexcloud.net/captcha.js',
        'area' => 'yasmartcaptcha_main',
    ],
    'client_key' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'yasmartcaptcha_main',
    ],
    'server_key' => [
        'xtype' => 'textfield',
        'value' => '',
        'area' => 'yasmartcaptcha_main',
    ],
    'send_user_ip' => [
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'yasmartcaptcha_main',
    ],
];