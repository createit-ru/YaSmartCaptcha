<?php

class YaSmartCaptcha
{
    private const VALIDATE_URL = "https://smartcaptcha.cloud.yandex.ru/validate";

    public modX $modx;
    private array $config;
    private array $initialized = [];
    private bool $enabled;

    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = [])
    {
        $this->modx =& $modx;
        $corePath = MODX_CORE_PATH . 'components/yasmartcaptcha/';
        $assetsUrl = MODX_ASSETS_URL . 'components/yasmartcaptcha/';

        $this->enabled = $this->modx->getOption('yasmartcaptcha_enabled', null, true);

        $this->config = array_merge([
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',

            'corePath' => $corePath,
        ], $config);

        $this->modx->lexicon->load('yasmartcaptcha:default');
    }

    /**
     * Initializes component into different contexts.
     *
     * @param string $ctx The context to load. Defaults to web.
     * @param array $scriptProperties
     *
     * @return bool
     */
    public function initialize(string $ctx = 'web', array $scriptProperties = []): bool
    {
        $this->config = array_merge($this->config, $scriptProperties);
        $this->config['ctx'] = $ctx;

        if (!empty($this->initialized[$ctx])) {
            return true;
        }

        switch ($ctx) {
            case 'mgr':
                break;
            default:
                if (!defined('MODX_API_MODE') || !MODX_API_MODE) {
                    $serviceJS = trim($this->modx->getOption('yasmartcaptcha_service_js'));
                    if (!empty($serviceJS)) {
                        $this->modx->regClientHTMLBlock('<script src="' . $serviceJS . '" defer></script>');
                    }

                }
                $this->initialized[$ctx] = true;
                break;
        }

        return true;
    }

    public function enabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Validates the token on the Yandex SmartCaptcha server.
     *
     * Network errors, timeouts and 5xx responses let the user pass (unless
     * the yasmartcaptcha_fail_open setting is disabled), so an outage of the
     * service does not block the forms. 4xx responses (e.g. a wrong server
     * key) and negative verdicts always fail.
     *
     * @param string $token
     * @return bool
     */
    public function validateToken(string $token): bool
    {
        if ($token === '') {
            return false;
        }

        $secret = $this->modx->getOption('yasmartcaptcha_server_key');

        if (empty($secret)) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, '[YaSmartCaptcha] System setting yasmartcaptcha_server_key is empty.');
            return false;
        }

        $args = [
            'secret' => $secret,
            'token' => $token,
        ];

        if ($this->modx->getOption('yasmartcaptcha_send_user_ip', null, false)) {
            $ip = $this->getClientIp();
            if (!empty($ip)) {
                $args['ip'] = $ip;
            }
        }

        $ch = curl_init(self::VALIDATE_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $output = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($output === false || $httpCode >= 500) {
            return $this->serviceUnavailable("code=$httpCode; error=$curlError; response=" . (string)$output);
        }
        if ($httpCode !== 200) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, "[YaSmartCaptcha] Validation request rejected: code=$httpCode; response=$output");
            return false;
        }

        $resp = json_decode($output);
        if (!is_object($resp) || !isset($resp->status)) {
            return $this->serviceUnavailable("invalid response: $output");
        }
        if ($resp->status !== 'ok') {
            $message = $resp->message ?? '';
            $this->modx->log(xPDO::LOG_LEVEL_INFO, "[YaSmartCaptcha] Validation failed: $message");
            return false;
        }

        return true;
    }

    /**
     * Handles a failure of the validation service according to the fail_open setting.
     */
    private function serviceUnavailable(string $details): bool
    {
        $failOpen = (bool)$this->modx->getOption('yasmartcaptcha_fail_open', null, true);
        $this->modx->log(
            $failOpen ? xPDO::LOG_LEVEL_WARN : xPDO::LOG_LEVEL_ERROR,
            '[YaSmartCaptcha] Validation service error, ' . ($failOpen ? 'access allowed' : 'access denied') . ': ' . $details
        );
        return $failOpen;
    }

    /**
     * Get the true client IP. Returns an array of values:
     *
     * @return string
     */
    public function getClientIp(): string
    {
        $this->modx->getRequest();
        $ipInfo = $this->modx->request->getClientIp();
        return array_key_exists('ip', $ipInfo) ? $ipInfo['ip'] : '';
    }
}