<?php

namespace AmoCRM;

use AmoCRM\Models\ModelInterface;
use AmoCRM\Request\CurlHandle;
use AmoCRM\Request\ParamsBag;
use AmoCRM\Helpers\Fields;
use AmoCRM\Helpers\Format;

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
 * @property \AmoCRM\Models\Call $call
 * @property \AmoCRM\Models\Catalog $catalog
 * @property \AmoCRM\Models\CatalogElement $catalog_element
 * @property \AmoCRM\Models\Company $company
 * @property \AmoCRM\Models\Contact $contact
 * @property \AmoCRM\Models\Customer $customer
 * @property \AmoCRM\Models\CustomersPeriods $customers_periods
 * @property \AmoCRM\Models\CustomField $custom_field
 * @property \AmoCRM\Models\Lead $lead
 * @property \AmoCRM\Models\Links $links
 * @property \AmoCRM\Models\Note $note
 * @property \AmoCRM\Models\Pipelines $pipelines
 * @property \AmoCRM\Models\Task $task
 * @property \AmoCRM\Models\Transaction $transaction
 * @property \AmoCRM\Models\Unsorted $unsorted
 * @property \AmoCRM\Models\Webhooks $webhooks
 * @property \AmoCRM\Models\Widgets $widgets
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
     * @var CurlHandle Экземпляр CurlHandle для повторного использования
     */
    private $curlHandle;

    /**
     * Client constructor
     *
     * @param string $domain Поддомен или домен amoCRM
     * @param string $login Логин amoCRM
     * @param string $apikey Ключ пользователя amoCRM
     * @param string|null $proxy Прокси сервер для отправки запроса
     */
    public function __construct($domain, $login, $apikey, $proxy = null)
    {
        // Разернуть поддомен в полный домен
        if (strpos($domain, '.') === false) {
            $domain = sprintf('%s.amocrm.ru', $domain);
        }

        $this->parameters = new ParamsBag();
        $this->parameters->addAuth('domain', $domain);
        $this->parameters->addAuth('login', $login);
        $this->parameters->addAuth('apikey', $apikey);

        if ($proxy !== null) {
            $this->parameters->addProxy($proxy);
        }

        $this->fields = new Fields();

        $this->curlHandle = new CurlHandle();
    }

    /**
     * Возвращает экземпляр модели для работы с amoCRM API
     *
     * @param string $name Название модели
     * @return ModelInterface
     * @throws ModelException
     */
    public function __get($name)
    {
        $classname = '\\AmoCRM\\Models\\' . Format::camelCase($name);

        if (!class_exists($classname)) {
            throw new ModelException('Model not exists: ' . $name);
        }

        // Чистим GET и POST от предыдущих вызовов
        $this->parameters->clearGet()->clearPost();

        return new $classname($this->parameters, $this->curlHandle);
    }
}
