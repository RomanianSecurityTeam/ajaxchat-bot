<?php

chdir(__DIR__);
require 'vendor/autoload.php';
require 'src/helpers.php';

$cmd = new Commando\Command();
$cmd->option('f')->aka('flood')->boolean();

if($cmd['flood']){
	require 'flood.php';
	exit;
}

$robo = new Bot\Robo(['sambiubowitz2356', 'wkqitlwwkg-A1']);
$robo->loop_messages();










