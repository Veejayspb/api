<?php

namespace veejay\api\helper;

class StringHelper
{
    /**
     * Разобрать строку с параметрами.
     * Метод идентичен parse_str(), но возвращает результат.
     * @param string $query - строка вида: name=one&surname=two
     * @return array
     * @see parse_str()
     */
    public static function parseStr(string $query): array
    {
        parse_str($query, $data);
        return $data;
    }
}
