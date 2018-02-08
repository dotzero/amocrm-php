<?php

namespace AmoCRM\Models\Traits;

trait SetDateCreate
{
    /**
     * Сеттер для даты создания
     *
     * @param string $date Дата в произвольном формате
     * @return $this
     */
    public function setDateCreate($date)
    {
        $this->values['date_create'] = strtotime($date);

        return $this;
    }
}
