<?php
/**
 * КГ Подкаст Парсер
 *
 * @package   KG
 * @author    Sergey Ivanov <coldfs.ru@gmail.com>
 * @copyright 2014 coldfs
 */

if (php_sapi_name() !== "cli") {
    echo 'Only cli mode allowed';
    die;
}

//Fuck autolad
require_once('scripts/Database.php');
require_once('scripts/Logger.php');
require_once('scripts/Kg_Podcast.php');
require_once('scripts/Kg_Podcast_Part.php');
require_once('scripts/Kg_Podcast_Parser.php');
require_once('scripts/Kg_Ffmpeg_Helper.php');
require_once('scripts/Kg_Command.php');

// Парсим фаил с подкастом
// Берем рсс тунца (у фидбарнера)
$config = [
    'url' => 'http://feeds.feedburner.com/kino-govno/lasershow',
    'limit' => 0,
    'offset' => 0,
    'sourceDir' => 'www/audio/source/',
    'outputDir' => 'www/audio/chunked/'
];


if (isset($argv[1])) {
    $config['limit'] = intval($argv[1]);
}

if (isset($argv[2])) {
    $config['offset'] = intval($argv[2]);
}

Logger::log('start parsing', 'parse');

$parser = new Kg_Podcast_Parser();
$result = $parser->parseRss($config['url'], $config['limit'], $config['offset']);

Logger::log('parsing complete', 'parse');
Logger::log('start saving', 'save');

foreach ($result as $item) {
    $currentVersion = Database::getById($item->id);

    if (!empty($currentVersion)) {
        if ($currentVersion->hash === $item->hash) {
            Logger::log('-- ' . $item->subtitle . " unchanged, skip");
            continue;
        }
    }

    $item->status = 'new';
    Database::save($item);
    Logger::log('-- ' . $item->subtitle . " saved");
}

Database::saveAll();
Logger::log('save complete', 'save');

//prepare links for wget and commands for mpeg
Logger::log("prepare commands", 'com');
$podcasts = Database::getByStatus('new');

$wget = new Kg_Command('commands/wget.list');
$ffmpeg = new Kg_Command('commands/ffmpeg.list');

foreach ($podcasts as $podcast) {
    //
    $wget->add($podcast->getDownloadLink());

    foreach ($podcast->content as $k => $part) {
        $ffmpeg->add(Kg_Ffmpeg_Helper::avget($podcast, $part, $k, $config));
    }

    $podcast->status = 'inprogress';
    Database::save($podcast);
    Logger::log('-- ' . $podcast->subtitle . " processed");
}

$wget->write();
$ffmpeg->write();
Database::saveAll();

Logger::log("processing complete", 'com');
