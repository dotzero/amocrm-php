<?php
require __DIR__ . '/artifacts/amocrm.phar';

$models = [
    ['account', '\AmoCRM\Models\Account'],
    ['company', '\AmoCRM\Models\Company'],
    ['contact', '\AmoCRM\Models\Contact'],
    ['customer', '\AmoCRM\Models\Customer'],
    ['customers_periods', '\AmoCRM\Models\CustomersPeriods'],
    ['lead', '\AmoCRM\Models\Lead'],
    ['note', '\AmoCRM\Models\Note'],
    ['task', '\AmoCRM\Models\Task'],
    ['pipelines', '\AmoCRM\Models\Pipelines'],
    ['unsorted', '\AmoCRM\Models\Unsorted'],
    ['widgets', '\AmoCRM\Models\Widgets'],
    ['webhooks', '\AmoCRM\Models\Webhooks'],
];

$amo = new \AmoCRM\Client('example', 'login', 'hash');

foreach ($models as $model) {
    echo $amo->{$model[0]} . "\n";
    assert($amo->{$model[0]} instanceof $model[1]);
}