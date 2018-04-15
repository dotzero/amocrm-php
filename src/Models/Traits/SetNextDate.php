<?php

namespace AmoCRM\Models\Traits;

trait SetNextDate
{
    /**
     * Сеттер для ожидаемой даты
     *
     * @param string $date Дата в произвольном формате
     * @return $this
     */
    public function setNextDate($date)
    {
        $this->values['next_date'] = strtotime($date);

        return $this;
    }
}
