<?php
require dirname(__DIR__).'/vendor/autoload.php';
BX\Config\Config::init('yaml_file',dirname(__DIR__).'/config/main.yml');
BX\MVC\SiteController::run()->end();
