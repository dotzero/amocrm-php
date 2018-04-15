# Клиент для работы с API amoCRM

![amoCRM](https://raw.githubusercontent.com/dotzero/amocrm-php/master/assets/logo.png)

[![Build Status](https://travis-ci.org/dotzero/amocrm-php.svg?branch=master)](https://travis-ci.org/dotzero/amocrm-php)
[![Latest Stable Version](https://poser.pugx.org/dotzero/amocrm/version)](https://packagist.org/packages/dotzero/amocrm)
[![Total Downloads](https://poser.pugx.org/dotzero/amocrm/downloads)](https://packagist.org/packages/dotzero/amocrm)
[![License](https://poser.pugx.org/dotzero/amocrm/license)](https://packagist.org/packages/dotzero/amocrm)
[![Code Coverage](https://scrutinizer-ci.com/g/dotzero/amocrm-php/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/dotzero/amocrm-php/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dotzero/amocrm-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dotzero/amocrm-php/?branch=master)
[![Say Thanks!](https://img.shields.io/badge/Say%20Thanks-!-1EAEDB.svg)](https://saythanks.io/to/dotzero)

Удобный и быстрый клиент на PHP для работы с API [amoCRM](https://www.amocrm.ru/), реализующий все методы оригинального API.

## Внимание! Не актуальные ссылки на документацию

Данный пакет взаимодействует со старой версией API. Но это не значит, что это API более не поддерживается. Это полностью рабочее API, которое не собираются удалять, просто ссылки более не актуальные, к сожалению на данный момент единственным решением будет просмотр документации тут:

https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/

Переход на новую версию API не быстрый и займет много времени.

[![Say Thanks!](https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png)](https://www.buymeacoffee.com/dotzero)

## Установка

### Через composer:

```bash
$ composer require dotzero/amocrm
```

или добавить

```json
"dotzero/amocrm": "0.3.*"
```

в секцию `require` файла composer.json.

### Без использования composer:

Скачать последнюю версию [amocrm.phar](https://github.com/dotzero/amocrm-php/releases/latest).

```php
<?php
// Использовать ее вместо vendor/autoload.php
require_once __DIR__ . '/amocrm.phar';
```

## Быстрый старт

```php
try {
    // Создание клиента
    $amo = new \AmoCRM\Client('SUBDOMAIN', 'LOGIN', 'HASH');

    // SUBDOMAIN может принимать как часть перед .amocrm.ru,
    // так и домен целиком например test.amocrm.ru или test.amocrm.com

    // Получение экземпляра модели для работы с аккаунтом
    $account = $amo->account;

    // Вывод информации об аккаунте
    print_r($account->apiCurrent());

    // Получение экземпляра модели для работы с контактами
    $contact = $amo->contact;

    // Заполнение полей модели
    $contact['name'] = 'ФИО';
    $contact['request_id'] = '123456789';
    $contact['date_create'] = '-2 DAYS';
    $contact['responsible_user_id'] = 697344;
    $contact['company_name'] = 'ООО Тестовая компания';
    $contact['tags'] = ['тест1', 'тест2'];

    // Добавление кастомного поля
    $contact->addCustomField(100, 'Значение');

    // Добавление кастомного поля с типом "мультисписок"
    $contact->addCustomMultiField(200, [
        1237755,
        1237757
    ]);

    // Добавление ENUM кастомного поля
    $contact->addCustomField(300, '+79261112233', 'WORK');

    // Добавление кастомного поля c SUBTYPE поля
    $contact->addCustomField(300, '+79261112233', false, 'subtype');

    // Добавление ENUM кастомного поля с типом "мультисписок"
    $contact->addCustomField(400, [
        ['+79261112233', 'WORK'],
    ]);

    // Добавление нового контакта и получение его ID
    print_r($contact->apiAdd());

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
```

## Список поддерживаемых моделей

- Аккаунт ([пример](examples/account.php), [документация](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/#account))
- Контакт ([пример](examples/contact.php), [документация](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/#contact))
- Сделка ([пример](examples/lead.php), [документация](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/#lead))
- Компания ([пример](examples/company.php), [документация](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/#company))
- Покупатель ([пример](examples/customer.php), [документация](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/#customer))
- Транзакция ([пример](examples/transaction.php), [документация](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/#transaction))
- Задача ([пример](examples/task.php), [документация](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/#tasks))
- Событие ([пример](examples/note.php), [документация](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/#event))
- Дополнительные поля ([пример](examples/custom_field.php), [документация](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/#fields))
- Звонок ([пример](examples/call.php), [документация](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/#call))
- Неразобранное ([пример](examples/unsorted.php), [документация](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/#unsorted))
- Webhooks ([пример](examples/webhooks.php), [документация](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/#webhooks))
- Воронки и этапы продаж ([пример](examples/pipelines.php), [документация](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/#pipelines))
- Периоды покупателей ([пример](examples/customers_periods.php), [документация](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/#customers_periods))
- Виджеты ([пример](examples/widgets.php), [документация](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/#widgets))
- Каталоги ([пример](examples/catalog.php), [документация](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/#catalogs))
- Элементы каталогов ([пример](examples/catalog_element.php), [документация](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/#catalog_elements))
- Связи ([пример](examples/links.php), [документация](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/#links))

## Описание методов моделей

- Модель `account` для работы с Аккаунтом

    * `apiCurrent($short = false)` - Получение информации по аккаунту в котором произведена авторизация
    * `getUserByLogin($login = null)` - Возвращает сведения о пользователе по его логину

- Модель `contact` для работы с Контактами

    * `apiList($parameters, $modified = null)` - Метод для получения списка контактов с возможностью фильтрации и постраничной выборки
    * `apiAdd($contacts = [])` - Метод позволяет добавлять контакты по одному или пакетно
    * `apiUpdate($id, $modified = 'now')` - Метод позволяет обновлять данные по уже существующим контактам
    * `apiLinks($parameters, $modified = null)` - Метод для получения списка связей между сделками и контактами

- Модель `lead` для работы со Сделками

    * `apiList($parameters, $modified = null)` - Метод для получения списка сделок с возможностью фильтрации и постраничной выборки
    * `apiAdd($leads = [])` - Метод позволяет добавлять сделки по одной или пакетно
    * `apiUpdate($id, $modified = 'now')` - Метод позволяет обновлять данные по уже существующим сделкам

- Модель `company` для работы с Компаниями

    * `apiList($parameters, $modified = null)` - Метод для получения списка компаний с возможностью фильтрации и постраничной выборки
    * `apiAdd($companies = [])` - Метод позволяет добавлять компании по одной или пакетно
    * `apiUpdate($id, $modified = 'now')` - Метод позволяет обновлять данные по уже существующим компаниям

- Модель `customer` для работы с Покупателями

    * `apiList($parameters)` - Метод для получения покупателей аккаунта
    * `apiAdd($customers = [])` - Метод позволяет добавлять покупателей по одному или пакетно
    * `apiUpdate($id)` - Метод позволяет обновлять данные по уже существующим покупателям

- Модель `transaction` для работы с Транзакциями

    * `apiList($parameters)` - Метод для получения транзакций аккаунта
    * `apiAdd($transactions = [])` - Метод позволяет добавлять транзакции по одной или пакетно
    * `apiDelete($id)` - Метод позволяет удалять транзакции

- Модель `task` для работы с Задачами

    * `apiList($parameters, $modified = null)` - Метод для получения списка задач с возможностью фильтрации и постраничной выборки
    * `apiAdd($tasks = [])` - Метод позволяет добавлять задачи по одной или пакетно
    * `apiUpdate($id, $text, $modified = 'now')` - Метод позволяет обновлять данные по уже существующим задачам

- Модель `note` для работы с Примечаниями (Задачами)

    * `apiList($parameters, $modified = null)` - Метод для получения списка примечаний с возможностью фильтрации и постраничной выборки
    * `apiAdd($notes = [])` - Метод позволяет добавлять примечание по одному или пакетно
    * `apiUpdate($id, $modified = 'now')` - Метод позволяет обновлять данные по уже существующим примечаниям

- Модель `custom_field` для работы с Дополнительными полями

    * `apiAdd($fields = [])` - Метод позволяет добавлять дополнительные поля по одному или пакетно
    * `apiDelete($id, $origin)` - Метод позволяет удалять дополнительные поля

- Модель `call` для работы со Звонками

    * `apiAdd($code, $key, $calls = [])` - Метод позволяет добавлять звонки по одному или пакетно

- Модель `unsorted` для работы со Списком неразобранных заявок

    * `apiList($parameters = [])` - Метод для получения списка неразобранных заявок с возможностью фильтрации и постраничной выборки
    * `apiGetAllSummary()` - Метод для получения агрегированной информации о неразобранных заявках
    * `apiAccept($uids, $user_id, $status_id = null)` - Метод для принятия неразобранных заявок
    * `apiDecline($uids, $user_id)` - Метод для отклонения неразобранных заявок
    * `apiAddSip($sip = [])` - Добавление неразобранных заявок с типом SIP
    * `apiAddMail($mails = [])` - Добавление неразобранных заявок с типом MAIL
    * `apiAddForms($forms = [])` - Добавление неразобранных заявок с типом FORMS
    * `addDataLead($values)` - Добавление сделки которая будет создана после одобрения заявки
    * `addDataContact($values)` - Добавление контакта или компании которая будет создана после одобрения заявки

- Модель `webhooks` для работы с Webhooks

    * `apiList()` - Метод для получения списка Webhooks
    * `apiSubscribe($url, $events = [])` - Метод для добавления Webhooks
    * `apiUnsubscribe($url, $events = [])` - Метод для удаления Webhooks

- Модель `pipelines` для работы с Списком воронок и этапов продаж

    * `apiList($id = null)` - Метод для получения списка воронок и этапов продаж
    * `apiAdd($pipelines = [])` - Метод позволяет добавлять воронки и этапов продаж по одной или пакетно
    * `apiUpdate($id)` - Метод позволяет обновлять данные по уже существующим воронкам и этапам продаж
    * `apiDelete($id)` - Метод позволяет удалять воронки по одной или пакетно
    * `addStatusField($parameters, $id = null)` - Добавление этапов воронки

- Модель `customers_periods` для работы с Компаниями

    * `apiList()` - Метод для получения списка периодов
    * `apiSet($periods = [])` - Метод позволяет изменять данные по периодам

- Модель `widgets` для работы с Виджетами

    * `apiList($parameters = [])` - Метод для получения списка доступных для установки виджетов
    * `apiInstall($parameters)` - Метод позволяет включать виджеты по одному или пакетно
    * `apiUninstall($parameters)` - Метод позволяет выключать виджеты по одному или пакетно

- Модель `catalog` для работы с Каталогами

    * `apiList($id = null)` - Метод для получения списка каталогов аккаунта
    * `apiAdd($catalogs = [])` - Метод позволяет добавлять каталоги по одному или пакетно
    * `apiUpdate($id)` - Метод позволяет обновлять данные по уже существующим каталогам
    * `apiDelete($id)` - Метод позволяет удалять данные по уже существующим каталогам

- Модель `catalog_element` для работы с Элементами каталога

    * `apiList($parameters = [])` - Метод для получения элементов каталога аккаунта
    * `apiAdd($elements = [])` - Метод позволяет добавлять элементы каталога по одному или пакетно
    * `apiUpdate($id)` - Метод позволяет обновлять данные по уже существующим элементам каталога
    * `apiDelete($id)` - Метод позволяет удалять данные по уже существующим элементам каталога

- Модель `links` для работы со Связями между сущностями

    * `apiList($parameters)` - Метод для получения связей между сущностями аккаунта
    * `apiLink($links = [])` - Метод позволяет устанавливать связи между сущностями
    * `apiUnlink($links = [])` - Метод позволяет удалять связи между сущностями

## Описание работы с Webhooks

[Webhooks](https://web.archive.org/web/20170801033744/https://developers.amocrm.ru/rest_api/webhooks.php) – это уведомление сторонних приложений посредством отправки уведомлений о событиях, произошедших в amoCRM. Вы можете настроить HTTP адреса ваших приложений и связанные с ними рабочие правила в настройках своего аккаунта, в разделе «API».

### Список доступных уведомлений

- `add_lead` - Добавить сделку
- `add_contact` - Добавить контакт
- `add_company` - Добавить компанию
- `add_customer` - Добавить покупателя
- `update_lead` - Изменить сделку
- `update_contact` - Изменить контакт
- `update_company` - Изменить компанию
- `update_customer` - Изменить покупателя
- `delete_lead` - Удалить сделку
- `delete_contact` - Удалить контакт
- `delete_company` - Удалить компанию
- `delete_customer` - Удалить покупателя
- `status_lead` - Смена статуса сделки
- `responsible_lead` - Смена ответственного сделки
- `restore_contact` - Восстановить контакт
- `restore_company` - Восстановить компанию
- `restore_lead` - Восстановить сделку
- `note_lead` - Примечание в сделке
- `note_contact` - Примечание в контакте
- `note_company` - Примечание в компании
- `note_customer` - Примечание в покупателе

```php
try {
    $listener = new \AmoCRM\Webhooks\Listener();

    // Добавление обработчика на уведомление contacts->add
    $listener->on('add_contact', function ($domain, $id, $data) {
        // $domain Поддомен amoCRM
        // $id Id объекта связанного с уведомлением
        // $data Поля возвращаемые уведомлением
    });

    // Вызов обработчика уведомлений
    $listener->listen();

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
```

## Описание хелпера Fields

Для хранения ID полей можно воспользоваться хелпером `Fields`

```php
try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Для хранения ID полей можно воспользоваться хелпером \AmoCRM\Helpers\Fields
    $amo->fields->StatusId = 10525225;
    $amo->fields->ResponsibleUserId = 697344;

    // Добавление сделок с использованием хелпера
    $lead = $amo->lead;
    $lead['name'] = 'Тестовая сделка';
    $lead['status_id'] = $amo->fields->StatusId;
    $lead['price'] = 3000;
    $lead['responsible_user_id'] = $amo->fields->ResponsibleUserId;
    $lead->apiAdd();

    // Также можно просто использовать хелпер без клиента
    $fields = new \AmoCRM\Helpers\Fields();

    // Как объект
    $fields->StatusId = 10525225;
    $fields->ResponsibleUserId = 697344;

    // Или как массив
    $fields['StatusId'] = 10525225;
    $fields['ResponsibleUserId'] = 697344;

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
```

## Описание хелпера B2BFamily

Хелпер для отправки письма через B2BFamily с привязкой к сделке в amoCRM

```php
try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    $b2b = new \AmoCRM\Helpers\B2BFamily(
        $amo,
        getenv('B2B_APPKEY'),
        getenv('B2B_SECRET'),
        getenv('B2B_EMAIL'),
        getenv('B2B_PASSWORD')
    );

    // Подписать клиента AmoCrm на Webhooks
    $b2b->subscribe();

    // Отправить письмо и прикрепить его к сделке
    $b2b->mail(6003277, [
        'to' => 'mail@example.com',
        'type' => 'message',
        'subject' => 'Тест b2bfamily',
        'text' => 'Тестовое сообщение',
        'events' => [
            'trigger' => 'message_open',
            'not_open_timeout' => 1
        ]
    ]);

} catch (\AmoCRM\Helpers\B2BFamilyException $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
```

## Интеграция с фреймворками

- Yii Framework 1.x ([yii-amocrm](https://github.com/dotzero/yii-amocrm))
- Yii Framework 2.x ([yii2-amocrm](https://github.com/dotzero/yii2-amocrm))
- Laravel 5.x ([laravel-amocrm](https://github.com/dotzero/laravel-amocrm))

## Тестирование

Для начала установить `--dev` зависимости. После чего запустить:

```bash
$ vendor/bin/phpunit
```

## Лицензия

Библиотека доступна на условиях лицензии MIT: http://www.opensource.org/licenses/mit-license.php
