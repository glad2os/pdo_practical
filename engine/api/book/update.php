<?php

use Exception\IllegalArgumentException;
use Database\PDO;

$pdo = new PDO();

$request = json_decode(file_get_contents("php://input"), true);

if ((!isset($request['id'])) ||
    (!isset($request['title'])) ||
    (!isset($request['isbn'])) ||
    (!isset($request['price'])) ||
    (!isset($request['authors']))
) throw new IllegalArgumentException("Fields must be exists");

$bookId = $request['id'];

$pdo->updateBook($bookId, $request['title'], $request['isbn'], $request['price']);

$currentAuthorsIds = $pdo->getAuthorsIdsOfBook($request['id']);
$newAuthorsIds = $request['authors'];
$toInsert = array_diff($newAuthorsIds, $currentAuthorsIds);
$toDelete = array_diff($currentAuthorsIds, $newAuthorsIds);

foreach ($toInsert as $author) {
    $pdo->linkAuthor($author, $bookId);
}

foreach ($toDelete as $author) {
    $pdo->unLinkAuthor($author, $bookId);
}
