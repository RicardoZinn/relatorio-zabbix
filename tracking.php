<?php 
header('Content-type: text/html; charset=ISO-8859-1');

// Allow access from anywhere. Can be domains or * (any)
header('Access-Control-Allow-Origin: *');

// Allow these methods of data retrieval
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

// Allow these header types
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

include "fhost.php";

$id = $_GET["id"];

if(!is_null($id)){
	$queryPrimeiroAcesso = registraPrimeiroAcesso($id);
	$queryUltimoAcesso = registraUltimoAcesso($id);
	$queryQtdAcesso = registraQtdAcesso($id);
	
	debugl("Primeiro Primeiro Acesso? $queryPrimeiroAcesso");
	debugl("Atribuiu Ultimo Acesso? $queryUltimoAcesso");
	debugl("Atribuiu QtdAcesso? $queryQtdAcesso");
}

function registraPrimeiroAcesso($id){
	$query 	= "UPDATE historico 
				SET data_primeiro_acesso = CURRENT_TIMESTAMP
				where id = $id 
				and data_primeiro_acesso is null
				and (data_envio is not null and data_revisao is not null)";
	return Update($query);
}

function registraQtdAcesso($id){
	$query 	= "UPDATE historico 
				SET qtd_acesso = qtd_acesso + 1				
				where id = $id 
				and (data_envio is not null and data_revisao is not null) ";
	return Update($query);
}

function registraUltimoAcesso($id){
	$query 	= "UPDATE historico 
				SET data_ultimo_acesso = CURRENT_TIMESTAMP
				where id = $id 
				and (data_envio is not null and data_revisao is not null) ";
	return Update($query);
}
?>