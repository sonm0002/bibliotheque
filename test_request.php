<?php
// Réception de 'q' en POST
if (isset($_POST['q'])) {
    header('Content-Type: text/plain') ;
    echo "Reçu '{$_POST['q']}' en POST, contenu retourné en texte" ;
    return ;
}

// Réception de 'q' en GET
if (isset($_GET['q'])) {
    header('Content-Type: text/plain') ;
    echo "Reçu '{$_GET['q']}' en GET, contenu retourné en texte" ;
    return ;
}

// Réception de 'json' en GET
if (isset($_GET['json'])) {
    header('Content-Type: application/json') ;
    echo json_encode(array('value' => "Reçu '{$_GET['json']}' en GET, contenu retourné en JSON")) ;
    return ;
}

// Réception de 'xml' en GET
if (isset($_GET['xml'])) {
    header('Content-Type: text/xml') ;
    echo <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<racine>
        <result>Reçu '{$_GET['xml']}' en GET, contenu retourné en XML</result>
</racine>
XML;
    return ;
}

// Réception de 'error' en GET
if (isset($_GET['error'])) {
    header('HTTP/1.1 400 Bad Request', true, 400) ;
    echo "Code erreur OK après réception de '{$_GET['error']}'" ;
    return ;
}

header('HTTP/1.1 501 Not Implemented', true, 501) ;
echo "Aucun paramètre reçu" ;