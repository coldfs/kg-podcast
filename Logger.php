<?php
/**
 * КГ Подкаст Парсер
 *
 * @package   KG
 * @author    Sergey Ivanov <coldfs.ru@gmail.com>
 * @copyright 2014 coldfs
 */

/**
 * Логгер
 */
class Logger
{
    public static $log = [];

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
