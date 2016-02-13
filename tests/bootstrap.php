<?php

$autoloader = require_once dirname(__FILE__) . '/../vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();
