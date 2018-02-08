<?php

namespace AmoCRM\Helpers;

/**
 * Class Format
 *
 * Хелпер для изменения формата данных
 *
 * @package AmoCRM\Helpers
 * @author denostr <4deni.kiev@gmail.com>
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Format
{
    /**
     * Приведение under_score к CamelCase
     *
     * @param string $string Строка
     * @return string Строка
     */
    public static function camelCase($string)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }
}
