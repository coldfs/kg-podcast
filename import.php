<?php
/**
 * КГ Подкаст Парсер
 *
 * @package   KG
 * @author    Sergey Ivanov <coldfs.ru@gmail.com>
 * @copyright 2014 coldfs
 */

//Fuck autolad
require_once('Database.php');
require_once('Logger.php');
require_once('Kg_Podcast.php');
require_once('Kg_Podcast_Part.php');
require_once('Kg_Podcast_Parser.php');
require_once('Kg_Ffmpeg_Helper.php');
require_once('Kg_Command.php');

// Парсим фаил с подкастом
// Берем рсс тунца (у фидбарнера)
$config = [
    'url' => 'http://feeds.feedburner.com/kino-govno/lasershow',
    'limit' => 10,
    'offset' => 1,
    'sourceDir' => 'audio/source/',
    'outputDir' => 'audio/chunked/'
];

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
        $ffmpeg->add(Kg_Ffmpeg_Helper::get($podcast, $part, $k, $config));
    }

    $podcast->status = 'inprogress';
    Database::save($podcast);
    Logger::log('-- ' . $podcast->subtitle . " processed");
}

$wget->write();
$ffmpeg->write();
Database::saveAll();

Logger::log("processing complete", 'com');
Logger::log("\n\nNow you must run process.sh");
