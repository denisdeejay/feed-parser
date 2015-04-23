<?php
/**
 * @package feed-parser
 * @author denisdeejay
 * Date: 23/04/2015
 * Time: 11:49
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Feed\Parser;

Parser::fromUrl('http://test');