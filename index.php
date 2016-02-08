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
	//define quando altera a categoria, criando um header ou grand-header (a definir)
	$query_categorias = 	"select distinct IdCategoria
							from v_relatorio
							where IdHost = " . $codhost . "
							order by OrdemCategoria";
				
		$rows = Select($query_categorias);	
	foreach ($rows as $categoria){
		//cria um header para cada categoria
		$query_subcategorias = "select distinct IdSubCategoria
								from v_relatorio
								where 	IdHost = " . $codhost . "
								and		IdCategoria = " . $categoria["IdCategoria"] . "
								order by OrdemSubCategoria";
								
		echo "Categoria :" . $categoria["IdCategoria"] . "</br>";
		
		$rows = Select($query_subcategorias);
		
		/*	para cada categoria pode ter v�rias outras subcategorias..
			Banco de dados (categoria), Instancia A (subcategoria), Instancia B (subcategoria)...
		*/
		foreach ($rows as $subcategoria){
			echo ".      subcategorias:".$subcategoria["IdSubCategoria"]."</br>";
		}	
	}	
}



/* FUNCAO SELECT
*  RECEBE UMA QUERY STRING
*  RETORNA UM ARRAY ASSOCIATIVO
* 
*  Exemplo: ARRAY["NOME_COLUNA"]
*/
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