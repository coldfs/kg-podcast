<?php
/**
 * КГ Подкаст Парсер
 *
 * @package   KG
 * @author    Sergey Ivanov <coldfs.ru@gmail.com>
 * @copyright 2014 coldfs
 */

/**
 * Хелпер для ffmpeg
 */
class Kg_Ffmpeg_Helper
{
    public static function get($podcast, $part, $k, $config)
    {
        $com = 'ffmpeg -y -i ' . $config['sourceDir'] . $podcast->getFileName() // Исходный фаил
            . ' -acodec copy -t ' .$part->length // Длительность записи
            . ' -ss ' . $part->start // Начало записи
            . ' -metadata title="' . $part->original .'" ' // Метатег с названием
            . $config['outputDir'] . ($podcast->id .'_' . $k) .'.mp3'; //Название фаила

        return $com;
    }
}
