<?php
header('Content-type: text/html; charset=ISO-8859-1');

// Allow access from anywhere. Can be domains or * (any)

header('Access-Control-Allow-Origin: *');

// Allow these methods of data retrieval

header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

// Allow these header types

header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

include "fhost.php";

$mailid = isset($_GET["mailid"]) ? $_GET["mailid"] : null;
$tracking = isset($_GET["tracking"]) ? $_GET["tracking"] : null;
$object = isset($_GET["object"]) ? $_GET["object"] : null;
$host = new stdClass;

if(!is_null($object)){
	$host = unserialize(base64_decode($object));
	echo processaRelatorioHost($mailid,$host);
}else if (is_null($mailid)) {
    echo ("<h1>Informe um codigo de email enviado</h1>");
    exit();
}
else {
    $resultado = RecuperaHistoricoGrafico($mailid);
    $host = unserialize(base64_decode($resultado));
    if (is_null($host->nome)) {
        echo ("<h1>Não encontramos o registro do e-mail ($mailid) para o host ($codhost) </br>:(");
        exit();
    }
	echo processaRelatorioHost($mailid,$host);
}

?>