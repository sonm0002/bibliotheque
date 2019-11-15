<?php

require_once 'autoload.inc.php';
require_once 'mypdo.inc.php';

$stmt = MyPDO::getInstance()->prepare(<<<SQL
    SELECT name
    FROM artist
    WHERE LOWER(name) LIKE CONCAT('%', :pattern, '%')
    ORDER BY name
SQL
);

$params = array(':pattern' => strtolower($_GET['q']));
$stmt->execute($params);

$artistList = array();
while (($artist = $stmt->fetch()) !== false) {
  $artistList[] = $artist['name'];
}

echo join(', ', $artistList);