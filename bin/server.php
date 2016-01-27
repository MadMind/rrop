<?php
$clientId = 'rrop-test';

require_once __DIR__.'/../vendor/autoload.php';

$server = new \MadMind\RROP\Server\Server($clientId);
$server->run();
