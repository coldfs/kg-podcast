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
class Kg_Podcast
{
    /**
     * @var string номер подкаста
     */
    public $id = 0;

    /**
     * @var string название подкаста
     */
    public $title = '';

    /**
     * @var string название выпуска
     */
    public $subtitle = '';

    /**
     * @var array содержимое подкаста
     */
    public $content = [];

    /**
     * @var string длительность
     */
    public $length = '00:00:00';

    /**
     * @var string Дата подкаста
     */
    public $date;

    /**
     * @var string статус
     */
    public $status = 'new';

    /**
     * @var string хеш
     */
    public $hash = '';


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
        if (!empty($this->content)) {
            $content = [];
            foreach ($this->content as $value) {
                $content[] = new Kg_Podcast_Part($value);
            }
            $this->content = $content;
        }
    }

    /**
     * Удовлетворяет ли поисковой строке
     *
     * @param string $query
     * @return bool
     */
    public function isMatch($query)
    {
        foreach ($this->content as $part) {
            if ($part->isMatch($query)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Получает хеш
     *
     * @return string
     */
    public function getHash()
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'content' => $this->content,
            'length' => $this->length,
        ];

        return md5(serialize($data));
    }


    public function getDownloadLink()
    {
        return 'http://kg/kg.' . $this->id . '.mp3';
    }

    public function getFileName()
    {
        return 'kg_' . $this->id . '.mp3';
    }
}
