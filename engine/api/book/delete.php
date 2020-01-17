<?php

use Exception\IllegalArgumentException;
use Database\PDO;

$mysqli = new PDO();

$request = json_decode(file_get_contents("php://input"), true);

if (!isset($request['id'])) throw new IllegalArgumentException("Field 'id' must be exists");

$mysqli->deleteBook($request['id']);
