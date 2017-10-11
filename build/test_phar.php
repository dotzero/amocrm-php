<?php
require __DIR__ . '/artifacts/amocrm.phar';

$models = [
    ['account', '\AmoCRM\Models\Account'],
    ['call', '\AmoCRM\Models\Call'],
    ['catalog', '\AmoCRM\Models\Catalog'],
    ['catalog_element', '\AmoCRM\Models\CatalogElement'],
    ['company', '\AmoCRM\Models\Company'],
    ['contact', '\AmoCRM\Models\Contact'],
    ['customer', '\AmoCRM\Models\Customer'],
    ['customers_periods', '\AmoCRM\Models\CustomersPeriods'],
    ['custom_field', '\AmoCRM\Models\CustomField'],
    ['lead', '\AmoCRM\Models\Lead'],
    ['links', '\AmoCRM\Models\Links'],
    ['note', '\AmoCRM\Models\Note'],
    ['pipelines', '\AmoCRM\Models\Pipelines'],
    ['task', '\AmoCRM\Models\Task'],
    ['transaction', '\AmoCRM\Models\Transaction'],
    ['unsorted', '\AmoCRM\Models\Unsorted'],
    ['webhooks', '\AmoCRM\Models\Webhooks'],
    ['widgets', '\AmoCRM\Models\Widgets'],
];

$amo = new \AmoCRM\Client('example', 'login', 'hash');

foreach ($models as $model) {
    echo $amo->{$model[0]} . "\n";
    assert($amo->{$model[0]} instanceof $model[1]);
}
