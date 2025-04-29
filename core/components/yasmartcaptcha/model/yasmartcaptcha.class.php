<?php

class YaSmartCaptcha
{
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
    public function initialize($ctx = 'web', $scriptProperties = array())
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
                    $placeholders = $this->makePlaceholders($this->config);

                    $serviceJS = trim($this->modx->getOption('yasmartcaptcha_service_js'));
                    if (!empty($serviceJS)) {
                        $serviceJS = str_replace($placeholders['pl'], $placeholders['vl'], $serviceJS);
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
     * @param string $token
     * @return bool
     */
    public function validateToken($token)
    {
        if (empty($token)) {
            return false;
        }

        $secret = $this->modx->getOption('yasmartcaptcha_server_key');

        if (empty($secret)) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, '[YaSmartCaptcha] System setting yasmartcaptcha_server_key is empty.');
            return false;
        }

        $url = "https://smartcaptcha.yandexcloud.net/validate";
        $args = [
            "secret" => $secret,
            "token" => $token,
        ];

        $useIP = $this->modx->getOption('yasmartcaptcha_send_user_ip', null, false);

        $ip = $this->getClientIp();
        if ($useIP && !empty($ip)) {
            $args['ip'] = $ip;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . "?" . http_build_query($args));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);

        $server_output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, "[YaSmartCaptcha] Allow access due to an error: code=$httpCode; message=$server_output");
            return false;
        }
        $resp = json_decode($server_output);
        return ($resp->status === "ok");
    }

    /**
     * Get the true client IP. Returns an array of values:
     *
     * @return string
     */
    public function getClientIp()
    {
        $this->modx->getRequest();
        $ip = $this->modx->request->getClientIp();
        return $ip['ip'];
    }

    /**
     * Transform array to placeholders
     *
     * @param array $array
     * @return array|array[]
     */
    private function makePlaceholders($array = [])
    {
        $prefix = '[[+';
        $suffix = ']]';
        $result = ['pl' => [], 'vl' => []];

        foreach ($array as $k => $v) {
            $result['pl'][$k] = $prefix . $k . $suffix;
            $result['vl'][$k] = $v;
        }

        return $result;
    }

}