<?php
/**
 * КГ Подкаст Парсер
 *
 * @package   KG
 * @author    Sergey Ivanov <coldfs.ru@gmail.com>
 * @copyright 2014 coldfs
 */

/**
 * Хранилище (на фаилах)
 */
class Database
{

    protected static $file = 'data.json';

    protected static $jsfile = 'data.js';

    protected static $changed = false;

    protected static $data = null;

    protected static function _open()
    {
        if (!file_exists(self::$file)) {
            file_put_contents(self::$file, '{}');
        }

        return json_decode(file_get_contents(self::$file), true);
    }


    protected static function _prepare($data)
    {
        $result = [];
        foreach ($data as $value) {
            $result[] = new Kg_Podcast($value);
        }
        return $result;
    }

    protected static function _loadAll()
    {
        if (is_null(self::$data)) {
            self::$data = self::_prepare(self::_open());
        }

        return self::$data;
    }

    protected static function _filter($closure)
    {
        $data = self::_loadAll();
        return array_filter($data, $closure);
    }

    public static function find($query)
    {
        $query = preg_quote($query);
        return self::_filter(function ($item) {
            return $item->isMatch($query);
        });
    }

    public static function getById($id)
    {
        $result = self::_filter(function ($item) use ($id) {
            return $item->id == $id;
        });
        //var_dump($result);
        if (!empty($result)) {
            return reset($result);
        } else {
            return null;
        }
    }

    public static function getByStatus($status = 'new')
    {
        return self::_filter(function ($item) use ($status) {
            return $item->status == $status;
        });
    }

    public static function save(Kg_Podcast $podcast)
    {
        $data = self::_loadAll();
        $insert = true;
        foreach ($data as $k => $value) {
            if ($value->id == $podcast->id) {
                $data[$k] = $podcast;
                $insert = false;
            }
        }

        if ($insert) {
            $data[] = $podcast;
        }

        self::$changed = true;
        self::$data = $data;
        return true;
    }

    public static function saveAll()
    {
        file_put_contents(self::$file, json_encode(self::$data));
        file_put_contents(self::$jsfile, 'var data = ' . json_encode(self::$data));
    }
}
