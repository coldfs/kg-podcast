<?php
/**
 * КГ Подкаст Парсер
 *
 * @package   KG
 * @author    Sergey Ivanov <coldfs.ru@gmail.com>
 * @copyright 2014 coldfs
 */

/**
 * Логгер (эта версия выводит прям в output)
 */
class Logger
{
    /**
     * @var array для замера времени
     */
    public static $log = [];

    /**
     * Выводит сообщение в лог
     *
     * @param string $message
     * @param string $tag - если не пустой, то при слудющем выводе с таким же тегом - выведет разницу во времени
     */
    public static function log($message, $tag = '')
    {
        $time = false;
        if (!empty($tag)) {
            $time = self::tag($tag);
        }

        if (!empty($time)) {
            echo $message . ' [' . $time . "]";
        } else {
            echo $message;
        }

        echo "\n";
    }


    /**
     * Стартует таймер для тега или отдает время этого тега
     *
     * @param string $tag
     * @return bool|float
     */
    public function tag($tag)
    {
        if (empty(self::$log[$tag])) {
            self::$log[$tag] = microtime(true);
            return false;
        }

        $time = microtime(true) - self::$log[$tag];
        $time = round($time, 3) . ' сек';

        unset(self::$log[$tag]);

        return $time;
    }
}
