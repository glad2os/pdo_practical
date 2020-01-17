<?php

use Exception\IllegalArgumentException;
use Database\PDO;

$mysqli = new PDO();

$request = json_decode(file_get_contents("php://input"), true);
if (!isset($request['name'])) throw new IllegalArgumentException("Fields must be exists");

$mysqli->addAuthor($request['name']);
