# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Что это

Дополнение (extra) для MODX Revolution: интеграция Yandex SmartCaptcha с формами FormIt / Login. Тестов, линтеров и npm-сборки в проекте нет. Пакет собирается стандартным MODX-транспортным билдером (`_build/`).

## Сборка

Билд требует работающей установки MODX: `_build/config.inc.php` ищет вверх по дереву каталогов `core/config/config.inc.php` и определяет `MODX_CORE_PATH`. Поэтому репозиторий нужно держать внутри дерева сайта MODX (или задать `MODX_CORE_PATH` вручную).

```
php _build/build.php            # собрать transport.zip в core/packages и сразу установить (install => true)
```

Через браузер `_build/build.php?download=1` отдаёт zip. Версия и релиз задаются в `_build/config.inc.php` (`version`, `release`); при выпуске обновляйте также `core/components/yasmartcaptcha/docs/changelog.txt`. Билдер подхватывает `docs/changelog.txt`, `license.txt`, `readme.txt` как атрибуты пакета.

## Архитектура

Логика живёт в `core/components/yasmartcaptcha/`:

- `model/yasmartcaptcha.class.php` — класс-сервис `YaSmartCaptcha` (загружается через `$modx->getService`). Отвечает за: подключение JS Яндекса (`regClientHTMLBlock` с URL из настройки `yasmartcaptcha_service_js`), флаг `enabled()`, серверную проверку токена `validateToken()` (cURL POST на `smartcaptcha.cloud.yandex.ru/validate`, таймаут 5 с; сетевые ошибки и 5xx пропускают пользователя при `fail_open` (по умолчанию включено), 4xx и вердикт не `ok` — всегда провал), определение IP клиента.
- `elements/snippets/yasmartcaptcha.php` — единый сниппет с двумя режимами: если в области видимости есть `$hook` и `$formit`/`$login`, работает как хук FormIt/Login (читает поле `smart-token`, при ошибке ставит ошибки с ключами `smart-token` и `yasmartcaptcha`); иначе выводит чанк с капчей. Регистрируется в `_build/elements/snippets.php`.
- `elements/chunks/yasmartcaptcha.tpl` — чанк `tpl.YaSmartCaptcha` (видимая капча, `<div class="smart-captcha" data-sitekey="[[+client_key]]">`). `yasmartcaptcha_invisible.tpl` — чанк `tpl.YaSmartCaptcha.Invisible` (контейнер `data-yasmartcaptcha` + hidden `smart-token`). Сниппет выбирает чанк по настройке `yasmartcaptcha_invisible`, если `&tpl` не передан (у свойства `tpl` в `_build/elements/snippets.php` намеренно пустое значение по умолчанию).
- `assets/components/yasmartcaptcha/js/yasmartcaptcha.js` — клиентский JS-API невидимой капчи (`YaSmartCaptcha.execute/reset`). Отправку формы он не перехватывает, её делает JS сайта. `initialize()` в невидимом режиме подключает его раньше скрипта Яндекса, к URL которого добавляется `?render=onload&onload=YaSmartCaptchaInit`.
- `lexicon/{en,ru}/` — строки; при добавлении ключей правьте оба языка. `setting.inc.php` содержит названия/описания системных настроек.

Пакетирование (`_build/`): `build.php` — класс `YaSmartCaptchaPackage`; определения элементов и системных настроек — в `_build/elements/*.php` (ключи настроек в `settings.php` без префикса, билдер добавляет `yasmartcaptcha_`); `_build/resolvers/` — резолверы, выполняемые при установке (файлы, начинающиеся с `_` или `.`, пропускаются). Билдер ожидает опциональные файлы `widgets.php`, `resources.php`, `plugins.php` и др. — их в репозитории нет, соответствующие методы не вызываются.

## Системные настройки (пространство `yasmartcaptcha`)

`enabled`, `service_js`, `client_key`, `server_key`, `send_user_ip`, `fail_open`, `invisible`. При `enabled = false` хук всегда возвращает `true`, рендер — пустую строку. При пустом `server_key` проверка всегда проваливается (с записью в лог).
