<?php

$_lang['area_yasmartcaptcha_main'] = 'Main';

$_lang['setting_yasmartcaptcha_enabled'] = 'Enable captcha';
$_lang['setting_yasmartcaptcha_enabled_desc'] = 'You can globally enable or disable captcha on the site, including its rendering on pages and checking in hooks.';

$_lang['setting_yasmartcaptcha_service_js'] = 'JS script for Yandex SmartCaptcha service';
$_lang['setting_yasmartcaptcha_service_js_desc'] = 'The component will add this script to the page. If you add the script manually, clear this field.';

$_lang['setting_yasmartcaptcha_invisible'] = 'Invisible captcha';
$_lang['setting_yasmartcaptcha_invisible_desc'] = 'Use the invisible captcha (no "I\'m not a robot" button). The check must be started from your site\'s JS: YaSmartCaptcha.execute(form). See README for details.';

$_lang['setting_yasmartcaptcha_client_key'] = 'Client key';
$_lang['setting_yasmartcaptcha_client_key_desc'] = 'You will receive this key after registering a new captcha in the service.';

$_lang['setting_yasmartcaptcha_server_key'] = 'Server key';
$_lang['setting_yasmartcaptcha_server_key_desc'] = 'You will receive this key after registering a new captcha in the service.';

$_lang['setting_yasmartcaptcha_fail_open'] = 'Allow access if the service is unavailable';
$_lang['setting_yasmartcaptcha_fail_open_desc'] = 'If the Yandex validation service does not respond or returns a 5xx error, let the user pass. Otherwise the form is rejected.';

$_lang['setting_yasmartcaptcha_send_user_ip'] = 'Transfer the user IP';
$_lang['setting_yasmartcaptcha_send_user_ip_desc'] = 'Transfer the user\'s IP to Yandex services.';
