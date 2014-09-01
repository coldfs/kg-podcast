<?php
/**
 * КГ Подкаст Парсер
 *
 * @package   KG
 * @author    Sergey Ivanov <coldfs.ru@gmail.com>
 * @copyright 2014 coldfs
 */

require_once('Kg_Podcast_Parser.php');
require_once('Kg_Podcast.php');
require_once('Kg_Podcast_Part.php');
require_once('Database.php');

// Парсим фаил с подкастом
// Берем рсс тунца (у фидбарнера)
$config = [
    'url' => 'http://feeds.feedburner.com/kino-govno/lasershow',
    //'url' => 'lasershow.html',
    'limit' => 0,
    'offset' => 0,
];


echo "start parsing\n\n";

$parser = new Kg_Podcast_Parser();
$result = $parser->parseRss($config['url'], $config['limit'], $config['offset']);

echo "parsing complete\n";
echo "start saving\n\n";

// Fuck it, plain text is our best choise
foreach ($result as $item) {
    $currentVersion = Database::getById($item->id);

    if (!empty($currentVersion)) {
        if ($currentVersion->hash === $item->hash) {
            echo $item->subtitle . " unchanged, skip\n";
            continue;
        }
    }

    $item->status = 'new';
    Database::save($item);
    echo $item->subtitle . " saved\n";
}

Database::saveAll();
echo "\nsave complete \n";
