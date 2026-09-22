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
    if (!$YaSmartCaptcha->enabled()) {
        return true;
    }
    // FormIt hook
    $modx->lexicon->load('yasmartcaptcha:default');

    $addErrors = static function (string $key) use ($hook, $modx) {
        $hook->addError('smart-token', $modx->lexicon($key));
        $hook->addError('yasmartcaptcha', $modx->lexicon($key));
    };

    $token = $hook->getValue('smart-token');
    if (!is_string($token) || $token === '') {
        $addErrors('yasmartcaptcha_token_empty');
        return false;
    }

    $validationResult = $YaSmartCaptcha->validateToken($token);
    if ($validationResult !== true) {
        $addErrors('yasmartcaptcha_validate_failed');
    }
    return $validationResult;
} else {
    // Render captcha
    if (!$YaSmartCaptcha->enabled()) {
        return '';
    }
    $YaSmartCaptcha->initialize($modx->context->get('key'), $scriptProperties);
    $tpl = $modx->getOption('tpl', $scriptProperties, '');
    if ($tpl === '') {
        $tpl = $YaSmartCaptcha->invisible() ? 'tpl.YaSmartCaptcha.Invisible' : 'tpl.YaSmartCaptcha';
    }
    return $modx->getChunk($tpl, [
        'client_key' => $modx->getOption('yasmartcaptcha_client_key')
    ]);
}