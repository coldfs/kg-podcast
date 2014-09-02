<?php
/**
 * КГ Подкаст Парсер
 *
 * @package   KG
 * @author    Sergey Ivanov <coldfs.ru@gmail.com>
 * @copyright 2014 coldfs
 */

/**
 * Класс подкаста
 */
class Kg_Podcast_Part
{
    /**
     * @var string Название
     */
    public $title;

    /**
     * @var string начало
     */
    public $start;

    /**
     * @var string длительность
     */
    public $length;

    /**
     * @var string исходная строка
     */
    public $original;


    /**
     * Конструктор
     *
     * @param array свойства
     */
    public function __construct($data = array())
    {
        foreach ($data as $field => $value) {
            if (property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }
    }

    /**
     * Подходит ли часть к поисковой строке
     *
     * @param string поисковая строка
     * @return bool
     */
    public function isMatch($query)
    {
        return preg_match('/' . $query .'/', $this->title);
    }

    /**
     * Возвращает объект в виде массива
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'title' => $this->title,
            'start' => $this->start,
            'length' => $this->length,
            'original' => $this->original
        ];
    }
}
