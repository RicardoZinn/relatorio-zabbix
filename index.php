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
	debug("<h1>Informe um codigo de hostname</h1>");
	exit();
}
else{
	//define quando altera a categoria, criando um header ou grand-header (a definir)
	$query_categorias = 	"select distinct IdCategoria,NomeCategoria
							from v_relatorio
							where IdHost = " . $codhost . "
							group by IdRelatorio, IdChave
							order by OrdemCategoria";
				
		$rows = Select($query_categorias);	

	debug("<ol type=\"1\">");	
	foreach ($rows as $categoria){
		//cria um header para cada categoria
		$query_subcategorias = "select distinct IdSubCategoria,NomeSubCategoria
								from v_relatorio
								where 	IdHost = " . $codhost . "
								and		IdCategoria = " . $categoria["IdCategoria"] . "
								group by IdRelatorio, IdChave
								order by OrdemSubCategoria";
	
		debug("<li><h1>Nivel Categoria(" . $categoria["NomeCategoria"] . ")</h1></li>");
		
		//echo "Categoria :" . $categoria["IdCategoria"] . "</br>";
		//echo "query_subcategorias -> $query_subcategorias</br>";
		
		$rows = Select($query_subcategorias);
		
		/*	para cada categoria pode ter varias outras subcategorias..
			Banco de dados (categoria), Instancia A (subcategoria), Instancia B (subcategoria)...
		*/
		debug("<ol type=\"1\">");
		foreach ($rows as $subcategoria){
			debug("<li><h2>Nivel SubCategoria(" . $subcategoria["NomeSubCategoria"] . ")</h2></li>");
			//echo ".      subcategorias:".$subcategoria["IdSubCategoria"]."</br>";
			
			$query_graficos = 	"select distinct IdGrafico,TituloGrafico
									from v_relatorio
									where IdHost = " . $codhost . "
									and IdCategoria = " . $categoria["IdCategoria"] . "
									and IdSubCategoria = " . $subcategoria["IdSubCategoria"] . "
									group by IdRelatorio, IdChave";
									
			//echo "query_graficos -> $query_graficos</br>";
			$rows = Select($query_graficos);
			
			debug("<ol type=\"1\">");	
			foreach ($rows as $grafico){	
				debug("<li><h3>Nome do grafico(" . $grafico["TituloGrafico"] . ")</h3></li>");
				$query_chaves = 	"select *
									from v_relatorio
									where IdHost = " . $codhost . "
									and IdCategoria = " . $categoria["IdCategoria"] . "
									and IdSubCategoria = " . $subcategoria["IdSubCategoria"] . "
									and IdGrafico = " . $grafico["IdGrafico"] . "
									group by IdRelatorio, IdChave
									order by 1,2,3";
				//echo "query_chaves -> $query_chaves</br>";
				$rows = Select($query_chaves);
				$string_auxiliar = null;
				debug("<ol type=\"1\">");
				
				(count($rows)>1) ? debug("<li><h4>Chaves de busca(".count($rows).") :</h4></li>") : debug("<li><h4>Chave de busca:</h4></li>");
				
				debug("<ol type=\"1\">");
				foreach ($rows as $chave){
					debug("<li><h4>Chave (" . $chave["Chave"] . ")</h4></li>");
										
					//Se essa chave possui objeto dinamico
					if(strpos($chave["Chave"], '%')){
						//inicia a variavel
						if(is_null($string_auxiliar))
							$string_auxiliar = " and (b.key_ like '" . $chave["Chave"] . "'";
						else			
						//concatena a variavel
							$string_auxiliar = $string_auxiliar . " or b.key_ like '" . $chave["Chave"] . "'";
					}
					//Se a chave nao possui objeto dinamico
					else{
						//inicia a variavel
						if(is_null($string_auxiliar))
							$string_auxiliar = " and (b.key_ = '" . $chave["Chave"] . "'";
						else			
						//concatena a variavel
							$string_auxiliar = $string_auxiliar . " or b.key_ = '" . $chave["Chave"] . "'";
					}					
				}
				debug("</ol>");
								
				if(!is_null($string_auxiliar))
					$string_auxiliar .= ") ";
				
				//busca as top 5 chaves na tabela do zabbix usando like '%%'
				//Aqui usamos tablespace apenas como nome de exemplo, mas na verdade pode ser qualquer
				//objeto que venha ser populado dinamicamente pelo zabbix auto-discovery
				$query_top5_keys = "select distinct x.key_ as Chave,tablespace_name
				from (select round(sum(value_max)/count(*),2) as media,b.key_,
				SUBSTRING(SUBSTRING_INDEX(b.key_,' ',-1), 1, CHAR_LENGTH(SUBSTRING_INDEX(b.key_,' ',-1)) - 1) as tablespace_name
				from zabbix.trends a,zabbix.items b, zabbix.hosts c
				where
				a.itemid = b.itemid
				and b.hostid = c.hostid
				and b.hostid = ". $codhost .
				$string_auxiliar . 
				//and b.key_ like '" . $chave["Chave"] . "'
				"and clock >=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY),interval -3 MONTH), '%Y%m%d%H%i%s')) 
				and clock <=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY), interval -1 second), '%Y%m%d%H%i%s')) 
				group by b.key_
				union 
				select 	round(sum(value_max)/count(*),2) as media,b.key_,
				SUBSTRING(SUBSTRING_INDEX(b.key_,' ',-1), 1, CHAR_LENGTH(SUBSTRING_INDEX(b.key_,' ',-1)) - 1) as tablespace_name
				from zabbix.trends_uint a,zabbix.items b, zabbix.hosts c
				where
				a.itemid = b.itemid
				and b.hostid = c.hostid
				and b.hostid = ". $codhost .
				$string_auxiliar . 
				//and b.key_ like '" . $chave["Chave"] . "'
				"and clock >=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY),interval -3 MONTH), '%Y%m%d%H%i%s')) 
				and clock <=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY), interval -1 second), '%Y%m%d%H%i%s')) 
				group by b.key_) x 
				order by media desc,tablespace_name limit 5";
				
				$rows = Select($query_top5_keys);
				
				$grafico_multiplo = null;
				if(count($rows)>1){
					$grafico_multiplo = true;
					debug("<li><h4>Chaves de retorno(".count($rows)."): <i>~buscara medias do objeto, plotando um data source [POR] objeto</i></h4></li>");
				}else{
					$grafico_multiplo = false;	
					debug("<li><h4>Chave de retorno: </b><i>~buscara detalhamento do objeto, plotando três data sources [PRO] objeto</i></h4></li>");
				}
				
				debug("<ol type=\"1\">");
				
				/** BUSCA OS VALORES PARA CADA CHAVE
				**/
				foreach ($rows as $oneOfTop5keys){
					debug("<li><h4>Chave de retorno (" . $oneOfTop5keys["Chave"] . ")</h4></li>");	
						debug("<ol type=\"1\">");						
						
						$query_valores_por_chave =
						"select round(min(value_min),2) as minima,
								round(max(value_max),2) as maxima,
								round(sum(value_max)/count(*),2) as media,
								date_format(FROM_UNIXTIME(clock),'%Y-%m') as data,
								b.key_ as key_
						from zabbix.trends a,zabbix.items b, zabbix.hosts c
						where
						a.itemid = b.itemid
						and b.hostid = c.hostid
						and b.hostid = ". $codhost ."
						and b.key_ = '" . $oneOfTop5keys["Chave"] . "' 
						and clock >=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY),interval -3 MONTH), '%Y%m%d%H%i%s')) 
						and clock <=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY), interval -1 second), '%Y%m%d%H%i%s')) 
						group by b.key_,date_format(FROM_UNIXTIME(clock),'%Y-%m')
						union 
						select 	round(min(value_min),2) as minima,
								round(max(value_max),2) as maxima,
								round(sum(value_max)/count(*),2) as media,
								date_format(FROM_UNIXTIME(clock),'%Y-%m') as data,
								b.key_ as key_
						from zabbix.trends_uint a,zabbix.items b, zabbix.hosts c
						where
						a.itemid = b.itemid
						and b.hostid = c.hostid
						and b.hostid = ". $codhost ." 
						and b.key_ = '" . $oneOfTop5keys["Chave"] . "' 
						and clock >=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY),interval -3 MONTH), '%Y%m%d%H%i%s')) 
						and clock <=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY), interval -1 second), '%Y%m%d%H%i%s')) 
						group by b.key_,date_format(FROM_UNIXTIME(clock),'%Y-%m') order by key_,data";
						
						$rows = Select($query_valores_por_chave);
						foreach ($rows as $valor){
							if($grafico_multiplo){
								debug("<li><h4>Data (" . $valor["data"] . ")&ensp;//&ensp;Media (" . $valor["maxima"] . ")</h4></li>");
							}								
							else{
								debug("<li><h4>Data (" . $valor["data"] . ")&ensp;//&ensp;Minima (" . $valor["minima"] . ")&ensp;
							//&ensp;Media (" . $valor["media"] . "&ensp;//&ensp;Maxima (" . $valor["maxima"] . ")</h4></li>");	
							}				
						}						
					debug("</ol>");
				}
				debug("</ol>");	
				debug("<li><h4>Query top 5 objetos ( " . $query_top5_keys . " )</h4></li>");
				debug("</ol>");
			}
			debug("</ol>");
		}	
		debug("</ol>");			
	}
	debug("</ol>");	
}

/* FUNCAO DEBUG
*  
*  VERIF. SE ESTA EM MODO DEBUG E IMPRIME CONTEUDO.
*/
function debug($text){
	$debug = $_GET["debug"];
	if($debug === strtolower("true")){
		echo($text);
	}
}

/* FUNCAO SELECT
*  RECEBE UMA QUERY STRING
*  RETORNA UM ARRAY ASSOCIATIVO
* 
*  Exemplo: ARRAY["NOME_COLUNA"]
*/
function Select($query){
	$ip = $_GET["ip"];
	$user = $_GET["user"];
	$password = $_GET["password"];
	$schema = $_GET["schema"];
	
	$conexao =  mysqli_connect("$ip","$user","$password","$schema");
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}else{
		$cursor = mysqli_query($conexao, $query);
		$resultado = mysqli_fetch_all($cursor, MYSQLI_ASSOC);
		return $resultado;	
	}
}

?>
	