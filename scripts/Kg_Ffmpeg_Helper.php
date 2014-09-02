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
    /**
     * Получает команду для ffmpeg
     *
     * @param Kg_Podcast $podcast
     * @param Kg_Podcast_Part $part
     * @param int $k - порядковый номер записи в покасте
     * @param array $config - настройки
     * @return string
     */
    public static function get($podcast, $part, $k, $config)
    {
        $com = 'ffmpeg -y -i ' . $config['sourceDir'] . $podcast->getFileName() // Исходный фаил
            . ' -acodec copy -t ' .$part->length // Длительность записи
            . ' -ss ' . $part->start // Начало записи
            . ' -metadata title="' . $part->original .'" ' // Метатег с названием
            . $config['outputDir'] . ($podcast->id .'_' . $k) .'.mp3'; //Название фаила

        return $com;
    }

    /**
     * Получает команду для avconv
     *
     * @param Kg_Podcast $podcast
     * @param Kg_Podcast_Part $part
     * @param int $k - порядковый номер записи в покасте
     * @param array $config - настройки
     * @return string
     */
    public static function avget($podcast, $part, $k, $config)
    {
        //TODO добавить нормальные IDv3 теги
        $com = 'avconv -i "' . $config['sourceDir'] . $podcast->getFileName() //Исходный фаил
            . '" -t ' . $part->length // Длительность записи
            . ' -ss '. $part->start // Начало записи
            . '  -acodec copy -y "' // Перезаписываем если нужно
            . $config['outputDir'] . ($podcast->id .'_' . $k) .'.mp3'.'"'; //Название фаила

        return $com;
    }
}
