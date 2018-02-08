<?php

namespace AmoCRM\Models\Traits;

trait SetLastModified
{
    /**
     * Сеттер для даты последнего изменения
     *
     * @param string $date Дата в произвольном формате
     * @return $this
     */
    public function setLastModified($date)
    {
        $this->values['last_modified'] = strtotime($date);

        return $this;
    }
}
