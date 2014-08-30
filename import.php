<?php
/**
 * КГ Подкаст Парсер
 *
 * @package   KG
 * @author    Sergey Ivanov <coldfs.ru@gmail.com>
 * @copyright 2014 coldfs
 */

require_once('Kg_Podcast_Parser.php');

// Парсим фаил с подкастом
// Берем рсс тунца (у фидбарнера)
$config = [
     //'url' => 'http://feeds.feedburner.com/kino-govno/lasershow',
    'url' => 'lasershow.html',
    'limit' => 0,
    'offset' => 0,
    'db' => 'KgPodcast'
];


echo "start parsing\n\n";

$parser = new Kg_Podcast_Parser();
$result = $parser->parseRss($config['url'], $config['limit'], $config['offset']);

echo "parsing complete\n";
echo "start saving\n\n";

// Old borring way
// $mongo = new MongoClient(); 
// $mongoDb = $mongo->selectDB($config['db']);
$podcasts = (new MongoClient())->selectDB($config['db'])->podcasts;

foreach ($result as $item) {
    $currentVersion = $podcasts->findOne(array('subtitle' => $item['subtitle']));

    if (!empty($currentVersion)) {
        if ($currentVersion['hash'] === $item['hash']) {
            echo $item['subtitle'] . " unchanged, skip\n";
            continue;
        }
        $item['_id'] = $currentVersion['_id'];
    }

    $item['status'] = 'new';
    $podcasts->save($item);
    echo $item['subtitle'] . " saved\n";
}

echo "\nsave complete \n";


$where = array('name' => array('$regex' => new MongoRegex("/^$search/")));