<?php

require_once 'autoload.inc.php';
require_once 'mypdo.inc.php';

$stmt = MyPDO::getInstance()->prepare(<<<SQL
    SELECT id, year, name
    FROM album
    WHERE artistId = :id
    ORDER BY 2
SQL
);

$params = array(':id' => $_GET['q']);
$stmt->execute($params);

$response =[];
while ( ($record = $stmt->fetch()) != false)
  $response[] = array('id' => $record['id'], 'txt' => "{$record['year']} - {$record['name']}");

echo json_encode($response, JSON_PRETTY_PRINT);