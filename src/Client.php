<?php

namespace AmoCRM;

use AmoCRM\Request\ParamsBag;
use AmoCRM\Helpers\Fields;

/**
 * Class Client
 *
 * Основной класс для получения доступа к моделям amoCRM API
 *
 * @package AmoCRM
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru/
 * @link https://github.com/dotzero/amocrm-php
 * @property \AmoCRM\Models\Account $account
 * @property \AmoCRM\Models\Company $company
 * @property \AmoCRM\Models\Contact $contact
 * @property \AmoCRM\Models\Customer $customer
 * @property \AmoCRM\Models\CustomersPeriods $customers_periods
 * @property \AmoCRM\Models\Lead $lead
 * @property \AmoCRM\Models\Note $note
 * @property \AmoCRM\Models\Task $task
 * @property \AmoCRM\Models\CustomField $custom_field
 * @property \AmoCRM\Models\Pipelines $pipelines
 * @property \AmoCRM\Models\Unsorted $unsorted
 * @property \AmoCRM\Models\Widgets $widgets
 * @property \AmoCRM\Models\WebHooks $webhooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Client
{
    /**
     * @var Fields|null Экземпляр Fields для хранения номеров полей
     */
    public $fields = null;

    /**
     * @var ParamsBag|null Экземпляр ParamsBag для хранения аргументов
     */
    public $parameters = null;

    /**
     * Client constructor
     *
     * @param string $domain Поддомен amoCRM
     * @param string $login Логин amoCRM
     * @param string $apikey Ключ пользователя amoCRM
     */
    public function __construct($domain, $login, $apikey)
    {
        $this->parameters = new ParamsBag();
        $this->parameters->addAuth('domain', $domain);
        $this->parameters->addAuth('login', $login);
        $this->parameters->addAuth('apikey', $apikey);

        $this->fields = new Fields();
    }

    /**
     * Возращает экземпляр модели для работы с amoCRM API
     *
     * @param string $name Название модели
     * @return mixed
     * @throws ModelException
     */
    public function __get($name)
    {
        $classname = '\\AmoCRM\\Models\\' . $this->toCamelCase($name);

        if (!class_exists($classname)) {
            throw new ModelException('Model not exists: ' . $name);
        }

        // Чистим GET и POST от предыдущих вызовов
        $this->parameters->clearGet()->clearPost();

        return new $classname($this->parameters);
    }

    /**
     * Приведение under_score к CamelCase
     *
     * @param string $string Строка
     * @return string Строка
     */
    private function toCamelCase($string)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }
}
