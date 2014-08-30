<?php
/**
 * КГ Подкаст Парсер
 *
 * @package   KG
 * @author    Sergey Ivanov <coldfs.ru@gmail.com>
 * @copyright 2014 coldfs
 */

/**
 * Класс Парсер (непонятно только нахуя он классом)
 */
class Kg_Podcast_Parser
{
    /**
     * Основная функция, Парсим рсс ленту
     *
     * @param string $filename имя фаила для парсинга (может быть ссылкой)
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function parseRss($filename, $limit = 0, $offset = 0)
    {
        $rss = file_get_contents($filename);
        // Обязательно предварительно заменить "itunes:" на "itunes"
        // лень гуглить как обращаться к нодам с двоеточием
        $rss = str_replace('itunes:', 'itunes', $rss);

        $xml = simplexml_load_string($rss);
        $result = [];
        foreach ($xml->channel->item as $item) {
            if ($offset > 0) {
                $offset--;
                continue;
            }

            $data = [
                'title' => (string) $item->title,
                'subtitle' => (string) $item->itunessubtitle,
                'date' => (string) $item->pubDate,
                'content' => $this->_parseSummary((string) $item->itunessummary, (string) $item->itunesduration),
            ];
            $data['hash'] = md5(serialize($data));

            $result[] = $data;

            $limit--;
            if (!$limit) {
                break;
            }

        }

        return $result;
    }


    /**
     * Разбивает содержание на элементы
     *
     * @param array $content
     * @param string $duration
     * @return array
     */
    protected function _parseSummary($content, $duration)
    {
        if (empty($content)) {
            return [];
        }

        $lines = explode("\n", $content);
        $result = [];
        foreach ($lines as $k => $value) {
            $parsed = $this->_parseLine($value);
            if (empty($parsed)) {
                continue;
            }

            // Заставляем первый элемент начнаться с 00:00:00. а то не понятно что делать с начальными огрызками
            if (empty($result)) {
                $parsed['start'] = '00:00:00';
            }
            $result[] = $parsed;
        }

        return $this->_addLengths($result, $duration);
    }


    /**
     * Парсит элемент содержания, разбивает на время и название
     *
     * @param string $line
     * @return string
     */
    protected function _parseLine($line)
    {
        $result = [];

        preg_match('/^((\d{1,2}:)?\d{1,2}:\d{1,2})\s*[—–]+\s*(.*)$/', $line, $matches);
        if (!empty($matches)) {
            $result['title'] = $matches[3];
            $result['start'] = $matches[1];
            $result['original'] = $line;
        } else {
            echo 'Не удалось распарсить: ', $line . "\n";
        }

        return $result;
    }


    /**
     * Вычисляет длинну каждого элемента в содержании
     *
     * @param array $content
     * @param string $duration
     * @return array
     */
    protected function _addLengths($content, $duration)
    {
        foreach ($content as $k => $value) {
            if (isset($content[$k + 1])) {
                $content[$k]['length'] = $this->_diffTimes($content[$k + 1]['start'], $content[$k]['start']);
            } else {
                $content[$k]['length'] = $this->_diffTimes($duration, $content[$k]['start']);
            }
        }
        return $content;
    }


    /**
     * Разница между двумя временными метками вида 00:00:00 или 00:00
     *
     * @param string $toTime
     * @param string $fromTime
     * @return string
     */
    protected function _diffTimes($toTime, $fromTime)
    {
        return gmdate("H:i:s", abs($this->_timeToSecconds($toTime) - $this->_timeToSecconds($fromTime)));
    }


    /**
     * Переводит время вида 00:00:00 или 00:00 в секунды.
     *
     * @param string $time
     * @return int
     */
    protected function _timeToSecconds($time)
    {
        $temp = explode(':', $time);

        if (!isset($temp[2])) {
            array_unshift($temp, '00');
        }

        return intval($temp[0]) * (60 * 60) + intval($temp[1]) * (60) + intval($temp[2]);
    }
}
