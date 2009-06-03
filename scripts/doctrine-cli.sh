#!/usr/bin/env php
<?php

// use development database settings
define('APPLICATION_ENV', 'cli');

require_once '../public/index.php';
$application->bootstrap('doctrine');

// run Doctrine CLI
$doctrine = Zend_Registry::get('doctrine');
$cli = new Doctrine_Cli($doctrine['paths']);
$cli->run($_SERVER['argv']);

?>
