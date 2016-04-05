<?php

$pdo = new \PDO(
    'mysql:host=db;dbname=demoDb',
    'demoUser',
    'demoPass'
);

# what's the right way to dump this?
var_dump($pdo);

phpinfo();