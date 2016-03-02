<?php
header('Content-type: text/html; charset=ISO-8859-1');

// Allow access from anywhere. Can be domains or * (any)
header('Access-Control-Allow-Origin: *');

// Allow these methods of data retrieval
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

// Allow these header types
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

include "fhost.php";


$codhost = $_GET["codhost"];
$tracking = $_GET["tracking"];
$preview = $_GET["preview"];

if(is_null($codhost)){
    debugl("<h1>Informe um codigo de hostname</h1>");
    exit();
}
else{
	/**
	* Cria o objeto de host com todos os valores possiveis.
	**/
	$identificador = generateID();	
	$host = createHost($codhost);	
	debugl("Grafico ID -> $identificador");
	debugl("Inseridos -> " . registraGeracaoHTML($identificador,$host));
	
	if(strcmp($preview,"true") == 0){
		echo processaRelatorioHost($identificador,$host);
	}
	echo "<h2>Id Gerado: $identificador<h2>";
}

?>
