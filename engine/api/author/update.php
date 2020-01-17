<?php

use Exception\IllegalArgumentException;
use Database\PDO;

$mysqli = new PDO();

$request = json_decode(file_get_contents("php://input"), true);
if (!isset($request['old']) || !isset($request['new']))
    throw new IllegalArgumentException("Fields 'old', 'new' must be exists");

if (!$mysqli->checkAuthor($request['old'])) throw new IllegalArgumentException("Author '${request['old']}' does not exist");
if ($mysqli->checkAuthor($request['new'])) throw new IllegalArgumentException("Author '${request['new']}' already exists");
$mysqli->updateAuthor($request['old'], $request['new']);
