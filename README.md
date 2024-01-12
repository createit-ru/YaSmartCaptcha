## YaSmartCaptcha

Это дополнение позволит использовать SmartCaptcha от Яндекс на вашем сайте на MODX в формех, созданных с использованием сниппета FormIt.

## Быстрый старт

### Регистрация в Yandex Cloud

Перед началом работы ознакомьтесь с инструкцией и зарегистрируйтесь в сервисе:
https://cloud.yandex.ru/ru/docs/smartcaptcha/quickstart

### Настройка компонента

Перейдите в системные настройки, выберите пространство yasmartcaptcha, и пропишите ключи:

* yasmartcaptcha_client_key — Клиентский ключ
* yasmartcaptcha_server_key — Серверный ключ

### Добавление капчи в форму

**Шаг 1.** В том месте, где вам нужно добавить капчу, добавьте некешированный вызов сниппета:

```
[[!YaSmartCaptcha]]
```

Данный сниппет подключит на страницу (перед закрывающимся body) скрипт
```
https://smartcaptcha.yandexcloud.net/captcha.js
```

и добавит html блок с капчей.

У сниппета единственный параметр ```tpl```, имеющий значение по-умолчанию ```tpl.YaSmartCaptcha```.

**Шаг 2.** Добавьте хук ```YaSmartCaptcha``` к ```FormIt```, например:
```
[[!FormIt?
&hooks=`YaSmartCaptcha,email`
..
]]
```

Если проверка не будет пройдена, то хук установит 2 ошибки с ключами smart-token и yasmartcaptcha.

Можете использовать любой ключ для показа ошибки, они равнозначны, первая соответствует названию hidden поля, а вторая названию компонента.