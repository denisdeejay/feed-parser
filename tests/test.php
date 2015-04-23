<?php
/**
 * @package feed-parser
 * @author denisdeejay
 * Date: 23/04/2015
 * Time: 11:49
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Feed\Parser;

header('Content-Type: text/html; charset=utf-8');

$rss = new Parser('http://www.vousfinancer.com/blog/feed/');
//var_dump($rss->getXmlFeed());
var_dump($rss->getTitle());
var_dump($rss->getLink());
var_dump($rss->getPubDate());
var_dump($rss->getItemsAsArray());

$atom = new Parser();
$atom->setXmlFeed('https://news.google.com/news?pz=1&cf=all&ned=fr&hl=fr&output=atom');
var_dump($atom->getTitle());
var_dump($atom->getLink());
var_dump($atom->getPubDate());
echo '<pre>';
print_r($atom->getItemsAsArray());
