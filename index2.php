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
$mesesAnalisados = 3;
$mesesAnalisadosEventos = 1;
$seriesAnalisadas = 5;

if(is_null($codhost)){
	debug("<h1>Informe um codigo de hostname</h1>");
	exit();
}
else{
	inicio();
}


function inicio(){
	global $codhost;
	
	$ArrayCategorias = buscaCategorias($codhost);
	foreach ($ArrayCategorias as $categoria){
		debugl("+" . $categoria["NomeCategoria"]);
		
		$ArraySubCategorias = buscaSubCategorias($categoria["IdCategoria"]);
		foreach ($ArraySubCategorias as $subCategoria){
			debugl("++" . $subCategoria["NomeSubCategoria"]);
			
			$ArrayGraficos = buscaGraficos($categoria["IdCategoria"],$subCategoria["IdSubCategoria"]);
			foreach ($ArrayGraficos as $grafico){
				debugl("+++" . $grafico["TituloGrafico"]);
				
				$ArrayChavesCadastradas = buscaChavesCadastradas($categoria["IdCategoria"],$subCategoria["IdSubCategoria"],$grafico["IdGrafico"]);
				foreach ($ArrayChavesCadastradas as $chave){
				debugl("++++" . $chave["Chave"]);	
				}	
				
				$ArrayCincoMaioresChaves = buscaCincoMaioresChaves($ArrayChavesCadastradas);
				$total = count($ArrayCincoMaioresChaves);
								
				foreach ($ArrayCincoMaioresChaves as $chaveMaior){
					debugl("+++++" . $chaveMaior["Chave"]);
					
					$ArrayValoresChave = buscaValoresPorChave($chaveMaior["Chave"]);					
					if($total>1){
						debugl("++++++ print apenas as medias");
					}else{
						debugl("++++++ print min/med/max");
					}												
				}								
			}			
		}
	}	
}



function buscaValoresPorChave($oneOfTop5keys){
	global $mesesAnalisados;
	global $codhost;
	
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
					and b.key_ = '" . $oneOfTop5keys . "' 
					and clock >=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY),interval -$mesesAnalisados MONTH), '%Y%m%d%H%i%s')) 
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
					and b.key_ = '" . $oneOfTop5keys . "' 
					and clock >=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY),interval -$mesesAnalisados MONTH), '%Y%m%d%H%i%s')) 
					and clock <=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY), interval -1 second), '%Y%m%d%H%i%s')) 
					group by b.key_,date_format(FROM_UNIXTIME(clock),'%Y-%m') order by key_,data";
	$rows = Select($query_valores_por_chave);
	return $rows;
}

function buscaCincoMaioresChaves($rows){
	global $codhost;
	global $seriesAnalisadas;	
	global $mesesAnalisados;
	$string_auxiliar = montaQueryAuxiliar($rows);
		
	$query_top5_keys = "select distinct x.key_ as Chave, object_dinamico
				from (select round(sum(value_max)/count(*),2) as media,b.key_,
				SUBSTRING(SUBSTRING_INDEX(b.key_,' ',-1), 1, CHAR_LENGTH(SUBSTRING_INDEX(b.key_,' ',-1)) - 1) as object_dinamico
				from zabbix.trends a,zabbix.items b, zabbix.hosts c
				where
				a.itemid = b.itemid
				and b.hostid = c.hostid
				and b.hostid = ". $codhost .
				$string_auxiliar . 
				"and clock >=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY),interval -$mesesAnalisados MONTH), '%Y%m%d%H%i%s')) 
				and clock <=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY), interval -1 second), '%Y%m%d%H%i%s')) 
				group by b.key_
				union 
				select 	round(sum(value_max)/count(*),2) as media,b.key_,
				SUBSTRING(SUBSTRING_INDEX(b.key_,' ',-1), 1, CHAR_LENGTH(SUBSTRING_INDEX(b.key_,' ',-1)) - 1) as object_dinamico
				from zabbix.trends_uint a,zabbix.items b, zabbix.hosts c
				where
				a.itemid = b.itemid
				and b.hostid = c.hostid
				and b.hostid = ". $codhost .
				$string_auxiliar . 
				"and clock >=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY),interval -$mesesAnalisados MONTH), '%Y%m%d%H%i%s')) 
				and clock <=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY), interval -1 second), '%Y%m%d%H%i%s')) 
				group by b.key_) x 
				order by media desc,object_dinamico limit $seriesAnalisadas";
				
	$rows = Select($query_top5_keys);
	return $rows;
}


function montaQueryAuxiliar($rows){
	foreach ($rows as $chave){							
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
	if(!is_null($string_auxiliar))
		$string_auxiliar .= ")";
	
	return $string_auxiliar;
}

function buscaChavesCadastradas($categoria,$subcategoria,$grafico){
	global $codhost;
	$query_chaves = 	"select *
						from v_relatorio
						where IdHost = " . $codhost . "
						and IdCategoria = " . $categoria . "
						and IdSubCategoria = " . $subcategoria . "
						and IdGrafico = " . $grafico . "
						group by IdRelatorio, IdChave
						order by 1,2,3";
	$rows = Select($query_chaves);
	return $rows;
}


function buscaGraficos($categoria,$subcategoria){
	global $codhost;
	$query_graficos = 	"select distinct 	IdGrafico,
											TituloGrafico, 
											Json
						from v_relatorio
						where IdHost = " . $codhost . "
						and IdCategoria = " . $categoria . "
						and IdSubCategoria = " . $subcategoria . "
						group by IdRelatorio, IdChave";						
	$rows = Select($query_graficos);
	return $rows;
}

function buscaSubCategorias($categoria){
	global $codhost;
	$query_subcategorias = "select distinct IdSubCategoria,
											NomeSubCategoria
							from v_relatorio
							where 	IdHost = " . $codhost . "
							and		IdCategoria = " . $categoria . "
							group by IdRelatorio, IdChave
							order by OrdemSubCategoria";
	$rows = Select($query_subcategorias);
	return $rows;
}

function buscaCategorias($codhost){
	$query_categorias = "select distinct IdCategoria,
										 NomeCategoria
						from v_relatorio
						where IdHost = " . $codhost . "
						group by IdRelatorio, IdChave
						order by OrdemCategoria";				
	$rows = Select($query_categorias);	
	return $rows;
}

function buscaEventos($chave){
	/*
	** Eventos disparados no periodo de 1 mes pela chave
	**/			
	$cod = 10227;
	global $mesesAnalisadosEventos;
	
	$query_eventos = "select count(*) quantidade
					from zabbix.items i
					left join zabbix.hosts h on i.hostid = h.hostid
					left join zabbix.functions f on i.itemid = f.itemid
					left join zabbix.triggers t on f.triggerid = t.triggerid
					left join zabbix.events e on  f.triggerid = e.objectid 
					where h.hostid = ". $cod ." 
					and i.key_ like '" . $chave . "' 
					and clock >=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY),interval -$mesesAnalisadosEventos MONTH), '%Y%m%d%H%i%s')) 
					and clock <=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY), interval -1 second), '%Y%m%d%H%i%s'))
					and t.status = 0
					and i.status = 0";
			
	$rows = Select($query_eventos);
	foreach ($rows as $evento){
		return $evento["quantidade"];
	}
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

/* FUNCAO DEBUG
*  
*  VERIF. SE ESTA EM MODO DEBUG E IMPRIME CONTEUDO.
*/
function debugl($text){
	$debug = $_GET["debug"];
	if($debug === strtolower("true")){
		echo($text."</br>");
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
	