<?php

require_once 'autoload.inc.php';
require_once 'mypdo.inc.php';

$stmt = MyPDO::getInstance()->prepare(<<<SQL
    SELECT DISTINCT artist.id, artist.name as 'txt'
    FROM artist, album
    WHERE artist.id = album.artistId
    AND album.genreId = :id
    ORDER BY 2
SQL
);

$params = array(':id' => $_GET['q']);
$stmt->execute($params);

echo json_encode($stmt->fetchAll(), JSON_PRETTY_PRINT);