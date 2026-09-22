<?php

return [
    'enabled' => [
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'yasmartcaptcha_main',
    ],
    'service_js' => [
        'xtype' => 'textfield',
        'value' => 'https://smartcaptcha.cloud.yandex.ru/captcha.js',
        'area' => 'yasmartcaptcha_main',
    ],
    'invisible' => [
        'xtype' => 'combo-boolean',
        'value' => false,
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
    'fail_open' => [
        'xtype' => 'combo-boolean',
        'value' => true,
        'area' => 'yasmartcaptcha_main',
    ],
    'send_user_ip' => [
        'xtype' => 'combo-boolean',
        'value' => false,
        'area' => 'yasmartcaptcha_main',
    ],
];