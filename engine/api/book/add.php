<?php

use Exception\IllegalArgumentException;
use Database\PDO;

$mysqli = new PDO();

$request = json_decode(file_get_contents("php://input"), true);

if ((!isset($request['title'])) ||
    (!isset($request['isbn'])) ||
    (!isset($request['price'])) ||
    (!isset($request['authors']))
) throw new IllegalArgumentException("Fields must be exists");

$bookId = $mysqli->addBook($request['title'], $request['isbn'], $request['price']);
foreach ($request['authors'] as $author) {
    $mysqli->linkAuthor($author, $bookId);
}
