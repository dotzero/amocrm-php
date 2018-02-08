<?php

namespace AmoCRM\Models\Traits;

trait SetLinkedLeadsId
{
    /**
     * Сеттер для списка связанных сделок
     *
     * @param int|array $value Номер связанной сделки или список сделок
     * @return $this
     */
    public function setLinkedLeadsId($value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $this->values['linked_leads_id'] = $value;

        return $this;
    }
}
