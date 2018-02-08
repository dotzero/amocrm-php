<?php

namespace AmoCRM\Models\Traits;

use AmoCRM\Models\Note;

trait SetNote
{
    /**
     * Сеттер для списка примечаний, которые появятся
     * после принятия неразобранного
     *
     * @param array|Note $value Примечание или массив примечаний
     * @return $this
     */
    public function setNotes($value)
    {
        $this->values['notes'] = [];

        if ($value instanceof Note) {
            $value = [$value];
        }

        foreach ($value as $note) {
            if ($note instanceof Note) {
                $note = $note->getValues();
            }

            $this->values['notes'][] = $note;
        }

        return $this;
    }
}
