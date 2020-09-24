<?php
/**
 * basic configuration file for whole project
 */

define('DEBUG', 5);
//use Furina\log\LogLevels;
//use Furina\core\Config;
//
//Config::$debug = LogLevels::DEBUG;
//
////error_reporting(E_ALL);
//ini_set('display_errors', 0);

$form = new \Mamba\tags\FormTag();
$form->registerTags();

$html = new \Mamba\tags\HtmlTag();
$html->registerTags();

//$form = new \Mamba\tags\HtmlTag();
//$form->registerTags();