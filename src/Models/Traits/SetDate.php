<?php

namespace AmoCRM\Models\Traits;

/**
 * Trait SetDate
 *
 * @package AmoCRM\Models\Traits
 * @author denostr <4deni.kiev@gmail.com>
 * @link https://github.com/dotzero/amocrm-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
trait SetDate
{
    /**
     * Сеттер для даты
     *
     * @param string $date Дата в произвольном формате
     * @return $this
     */
    public function setDate($date)
    {
        $this->values['date'] = strtotime($date);

        return $this;
    }
}
