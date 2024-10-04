<?php

/**
 * The main snippet of the YaSmartCaptcha component.
 * It can work as Formit Hook and renders captcha.
 */

/** @var modX $modx */
/** @var array $scriptProperties */
/** @var YaSmartCaptcha $YaSmartCaptcha */
$path = MODX_CORE_PATH . 'components/yasmartcaptcha/model/';
$YaSmartCaptcha = $modx->getService('YaSmartCaptcha', 'YaSmartCaptcha', $path, $scriptProperties);
if (!$YaSmartCaptcha) {
    $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not load YaSmartCaptcha class!');
    return false;
}

if ((isset($formit) || isset($login)) && isset($hook)) {
    // FormIt hook
    $modx->lexicon->load('yasmartcaptcha:default');

    $token = $hook->getValue('smart-token');
    if (empty($token)) {
        $hook->addError('smart-token', $modx->lexicon('yasmartcaptcha_token_empty'));
        $hook->addError('yasmartcaptcha', $modx->lexicon('yasmartcaptcha_token_empty'));
        return false;
    }

    $validationResult = $YaSmartCaptcha->validateToken($token);
    if ($validationResult !== true) {
        $hook->addError('smart-token', $modx->lexicon('yasmartcaptcha_validate_failed'));
        $hook->addError('yasmartcaptcha', $modx->lexicon('yasmartcaptcha_validate_failed'));
    }

    return $validationResult;
} else {
    // Render captcha
    $YaSmartCaptcha->initialize($modx->context->get('key'), $scriptProperties);
    $tpl = $modx->getOption('tpl', $scriptProperties, 'tpl.YaSmartCaptcha');
    return $modx->getChunk($tpl, [
        'client_key' => $modx->getOption('yasmartcaptcha_client_key')
    ]);
}