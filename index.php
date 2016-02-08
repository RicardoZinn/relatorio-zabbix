<?php
// Allow access from anywhere. Can be domains or * (any)
header('Access-Control-Allow-Origin: *');
// Allow these methods of data retrieval
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
// Allow these header types
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
//configuracoes do banco de dados
require_once('database.inc.php'); 
$codhost = $_GET["codhost"];
if(is_null($codhost)){
	echo "<h1>Informe um codigo de hostname</h1>";
	exit();
}
else{
	$graficos = "select 
						* 
					from 
						v_relatorio 
					where 
						IdHost = $codhost 
					order by OrdemCategoria, IdGrafico";
		
		//Execute
		$row = Select($graficos);
		echo "total graficos:$row";
}

function Select($q){
	$ip = $_GET["ip"];
	$user = $_GET["user"];
	$password = $_GET["password"];
	$schema = $_GET["schema"];
	
	$l = mysqli_connect("$ip","$user","$password","$schema");
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}else{
		$c = mysqli_query($l, $q);
		return mysqli_fetch_array($c,MYSQLI_ASSOC);
	}
}
?>