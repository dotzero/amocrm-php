# Клиент для работы с API amoCRM

[![Build Status](https://travis-ci.org/dotzero/amocrm-php.svg?branch=master)](https://travis-ci.org/dotzero/amocrm-php)
[![Latest Stable Version](https://poser.pugx.org/dotzero/amocrm/version)](https://packagist.org/packages/dotzero/amocrm)
[![License](https://poser.pugx.org/dotzero/amocrm/license)](https://packagist.org/packages/dotzero/amocrm)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dotzero/amocrm-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dotzero/amocrm-php/?branch=master)

Клиент для работы с API сервиса [amoCRM](https://www.amocrm.ru/)

## Установка

### Через composer:

```bash
$ composer require dotzero/amocrm
```

## Быстрый старт

```php
try {
    // Создание клиента
    $amo = new \AmoCRM\Client('SUBDOMAIN', 'LOGIN', 'HASH');

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
    $contact->addCustomField(448, [
        ['+79261112233', 'WORK'],
    ]);

    // Добавление нового контакта и получение его ID
    print_r($contact->apiAdd());

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}
```

## Список доступных моделей

- [Аккаунт](https://developers.amocrm.ru/rest_api/#account)
- [Контакт](https://developers.amocrm.ru/rest_api/#contact)
- [Сделка](https://developers.amocrm.ru/rest_api/#lead)
- [Компания](https://developers.amocrm.ru/rest_api/#company)
- [Задача](https://developers.amocrm.ru/rest_api/#tasks)
- [Событие](https://developers.amocrm.ru/rest_api/#event)

## Описание моделей и методов

- Модель `account` для работы с Аккаунтом

    * `apiCurrent($short = false)` - Получение информации по аккаунту в котором произведена авторизация

- Модель `contact` для работы с Контактами

    * `apiList($parameters, $modified = null)` - Метод для получения списка контактов с возможностью фильтрации и постраничной выборки
    * `apiAdd($contacts = [])` - Метод позволяет добавлять контакты по одному или пакетно
    * `apiUpdate($id, $modified = 'now')` - Метод позволяет обновлять данные по уже существующим контактам
    * `apiLinks($parameters, $modified = null)` - Метод для получения списка связей между сделками и контактами

- Модель `company` для работы с Компаниями

    * `apiList($parameters, $modified = null)` - Метод для получения списка компаний с возможностью фильтрации и постраничной выборки
    * `apiAdd($companies = [])` - Метод позволяет добавлять компании по одной или пакетно
    * `apiUpdate($id, $modified = 'now')` - Метод позволяет обновлять данные по уже существующим компаниям

- Модель `lead` для работы со Сделками

    * `apiList($parameters, $modified = null)` - Метод для получения списка сделок с возможностью фильтрации и постраничной выборки
    * `apiAdd($leads = [])` - Метод позволяет добавлять сделки по одной или пакетно
    * `apiUpdate($id, $modified = 'now')` - Метод позволяет обновлять данные по уже существующим сделкам

- Модель `note` для работы с Примечаниями (Задачами)

    * `apiList($parameters, $modified = null)` - Метод для получения списка примечаний с возможностью фильтрации и постраничной выборки
    * `apiAdd($notes = [])` - Метод позволяет добавлять примечание по одному или пакетно
    * `apiUpdate($id, $modified = 'now')` - Метод позволяет обновлять данные по уже существующим примечаниям

- Модель `task` для работы с Задачами

    * `apiList($parameters, $modified = null)` - Метод для получения списка задач с возможностью фильтрации и постраничной выборки
    * `apiAdd($tasks = [])` - Метод позволяет добавлять задачи по одной или пакетно
    * `apiUpdate($id, $text, $modified = 'now')` - Метод позволяет обновлять данные по уже существующим задачам

## Тестирование

Для начала установить `--dev` зависимости. После чего запустить:

```bash
$ vendor/bin/phpunit
```

## Лицензия

Библиотека доступна на условиях лицензии MIT: http://www.opensource.org/licenses/mit-license.php
