<?php

namespace AmoCRM\Models\Traits;

trait SetTags
{
    /**
     * Сеттер для списка тегов
     *
     * @param int|array $value Название тегов через запятую или массив тегов
     * @return $this
     */
    public function setTags($value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $types = array_unique(array_map('gettype', array_values($value)));
        if (count($types) == 1 && $types[0] == 'integer') {
            $this->values['tags'] = $value;
        } else {
            $this->values['tags'] = implode(',', $value);
        }

        return $this;
    }
}
