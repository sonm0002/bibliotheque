<?php

require_once 'autoload.inc.php';
require_once 'mypdo.inc.php';


/*
 * Tracks sorted by disknumber
 */ 
$stmt = MyPDO::getInstance()->prepare(<<<SQL
    SELECT disknumber, LPAD(number, 2, '0') as 'num', name, SUBSTR(SEC_TO_TIME(duration),4) as 'duration'
    FROM track, song
    WHERE song.id = track.songId
    AND albumId = :id
    ORDER BY 1, 2
SQL
);

$params = array(':id' => $_GET['q']);
$stmt->execute($params);

echo json_encode($stmt->fetchAll(), JSON_PRETTY_PRINT);