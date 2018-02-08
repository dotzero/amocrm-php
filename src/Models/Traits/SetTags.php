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

        $this->values['tags'] = implode(',', $value);

        return $this;
    }
}
