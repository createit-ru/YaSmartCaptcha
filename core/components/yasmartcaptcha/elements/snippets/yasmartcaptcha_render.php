<?php

/**
 * Render Yandex Smart Captcha
 */

/** @var modX $modx */
/** @var array $scriptProperties */
/** @var YaSmartCaptcha $YaSmartCaptcha */
$path = MODX_CORE_PATH . 'components/yasmartcaptcha/model/';
$YaSmartCaptcha = $modx->getService('YaSmartCaptcha', 'YaSmartCaptcha', $path, $scriptProperties);
if (!$YaSmartCaptcha) {
    $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not load YaSmartCaptcha class!');
    return '';
}

$YaSmartCaptcha->initialize($modx->context->get('key'), $scriptProperties);

$tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.YaSmartCaptcha');

return $modx->getChunk($tpl, [
    'client_key' => $modx->getOption('yasmartcaptcha_client_key')
]);