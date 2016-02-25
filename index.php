<?php
header('Content-type: text/html; charset=ISO-8859-1');

// Allow access from anywhere. Can be domains or * (any)
header('Access-Control-Allow-Origin: *');

// Allow these methods of data retrieval
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

// Allow these header types
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

//configuracoes do banco de dados
require_once('database.inc.php'); 


include "fheader.php";
include "fbody.php";
include "ffooter.php";

$codhost = $_GET["codhost"];
$mesesAnalisados = $_GET["meses"];
$mesesAnalisadosEventos = $_GET["meventos"];
$seriesAnalisadas = $_GET["items"];

$host = new stdClass;


if(is_null($codhost)){
	debug("<h1>Informe um codigo de hostname</h1>");
	exit();
}
else{
	setDefault();
	inicio();
	printHeader($host->nome);
	
	$total_categorias = count($host->categoria);
	$total_subcategoria = count($host->subcategoria);
	$total_json = count($host->ArrayJson);
	$total_eventos = count($host->ArrayEventos);
	
	debugl("total_categorias-> $total_categorias");
	debugl("total_subcategoria -> $total_subcategoria");
	debugl("total_json -> $total_json");
	debugl("total_eventos -> $total_eventos");
	
	$cria_header_categoria = false;
	$old_categoria = null;
	$cria_header_subcategoria = false;
	$old_subcategoria = null;
	for ($x = 0; $x < $total_categorias; $x++) {
		if(strcmp ( $old_categoria, $host->categoria[$x]) == 0)
			$cria_header_categoria = false;
		else
			$cria_header_categoria = true;
		
		if($cria_header_categoria){
			debugl("[CRIAR HEADER]". $host->categoria[$x]);
			echo "[CRIAR HEADER]". $host->categoria[$x] . "</br>";
			$old_categoria = $host->categoria[$x];
			$cria_header_categoria = false;
		}
	
		if(is_null($host->subcategoria[$x]))
			$cria_header_subcategoria = false;
		
		if(strcmp ( $old_subcategoria, $host->subcategoria[$x]) == 0)
			$cria_header_subcategoria = false;
		else
			$cria_header_subcategoria = true;
		
		if($cria_header_subcategoria){
			debugl("[CRIAR SUBHEADER]". $host->subcategoria[$x]);
			echo "[CRIAR SUBHEADER]". $host->subcategoria[$x];
			$old_subcategoria = $host->subcategoria[$x];
			$cria_header_subcategoria = false;
		}
		printBody($x,$host->ArrayJson[$x],$host->ArrayEventos[$x]);
	}
	printFooter($host->nomeEmpresa,$host->responsavel);
}


function inicio(){
	global $host;
	
	$dadosEmpresa = buscaEmpresa();
	$host->nomeEmpresa = $dadosEmpresa["empresa_nome"];
	$host->nome = $dadosEmpresa["host_nome"];
	$host->responsavel = $dadosEmpresa["responsavel_nome"];
	$host->emailResponsavel = $dadosEmpresa["responsavel_email"];
	$host->ArrayJson = [];
	$host->ArrayEventos = [];
	$host->categoria = [];
	$host->subcategoria = [];
	
	
	$ArrayCategorias = buscaCategorias();
	foreach ($ArrayCategorias as $categoria){
		debug("<hr>");
		debugl("[Categoria]" . $categoria["nome"]);
			
		$ArrayPossiveisInstancias = buscaPossiveisInstancias($categoria["categoria_id"]);	
		if(is_null($ArrayPossiveisInstancias))
			$ArrayPossiveisInstancias[]["subcategoria"] = null;
	
		foreach($ArrayPossiveisInstancias as $instancia){
			$nome_instancia = $instancia["subcategoria"];
			debugl("[SubCategoria]".$instancia["subcategoria"]);			
			$ArrayGraficos = buscaGraficos($categoria["categoria_id"]);
			
			
			foreach ($ArrayGraficos as $grafico){
				debug("<hr>");
				$json = $grafico["json"];
				$json = str_replace('$titulo_grafico',$grafico["titulo"],$json);
				$json = str_replace('$subtitulo_grafico',$grafico["subtitulo"],$json);
				
				//debugl("+++" . $grafico["titulo"]);
				//debugl("+++" . $grafico["subtitulo"]);
				//debugl("+++" . $json);
				
				$ArrayChavesCadastradas = buscaChavesCadastradas($grafico["grafico_id"]);
				foreach ($ArrayChavesCadastradas as $chave){
				debugl("[ChaveBusca]" . $chave["valor"]);	
				}	
				
				$ArrayCincoMaioresChaves = buscaCincoMaioresChaves($nome_instancia,$ArrayChavesCadastradas);
				$QtdEventos = buscaQtdEventos($ArrayChavesCadastradas);
				
				$total = count($ArrayCincoMaioresChaves);
				$series = Array();
				$categories = Array();
				
				foreach ($ArrayCincoMaioresChaves as $chaveMaior){
					debugl("[ChaveRetorno]" . $chaveMaior["Chave"]);
					
					/*
					$SubCategoria = buscaRegexSubCategoria($chaveMaior["Chave"]);
					$Objeto = buscaRegexObjeto($chaveMaior["Chave"]);
					debugl("++++++" . $SubCategoria);
					debugl("++++++" . $Objeto);
					*/
					
					$ArrayValoresChave = buscaValoresPorChave($chaveMaior["Chave"]);					
					if($total>1){
						debugl("[TipoPlotagem]"."print apenas as medias");
						array_push($series,processaValoresMultiplos($Objeto,$ArrayValoresChave));
						//debugl(json_encode($series, JSON_NUMERIC_CHECK));
					}else{
						debugl("[TipoPlotagem]"."print min/med/max");
						$series = processaMedias($ArrayValoresChave);
						//debugl(json_encode($series, JSON_NUMERIC_CHECK));
					}	
					
					//processa as datas				
					$categories	= processaDatas($ArrayValoresChave);			
				}
				$json = str_replace('$categorias',json_encode($categories, JSON_NUMERIC_CHECK),$json);
				
				//adiciona as series no json
				$json = str_replace('$series',json_encode($series, JSON_NUMERIC_CHECK),$json);
				//debugl("? json ? ".$json);
				//debugl("? eventos ? ".$QtdEventos);
				
				array_push($host->categoria,$categoria["nome"]);
				array_push($host->subcategoria,$instancia["subcategoria"]);
				array_push($host->ArrayJson,$json);
				array_push($host->ArrayEventos,$QtdEventos);			
			}
		}					
	}	
}

function setDefault(){
	global $mesesAnalisados,$mesesAnalisadosEventos,$seriesAnalisadas;
	
	if(is_null($mesesAnalisados))
		$mesesAnalisados = 3;	
	if(is_null($mesesAnalisadosEventos))
		$mesesAnalisadosEventos = 1;	
	if(is_null($seriesAnalisadas))
		$seriesAnalisadas = 5;
}

function buscaPossiveisInstancias($categoria_id){
	global $codhost;
	global $mesesAnalisados;
	
	$query_otimizadora = "select distinct valor 
							from view_grafico_host
							where host_id = $codhost
							and categoria_id = $categoria_id
							and (valor like '%\%%' or valor like '% % %')";
	
	$string_auxiliar = montaQueryAuxiliar(Select($query_otimizadora));
	
	if(is_null($string_auxiliar))
		return null;
		
	$query_possiveis_instancias =
	"SELECT DISTINCT
    SUBSTRING(key_,
        LOCATE(' ', key_),
        LOCATE(' ', key_, (LOCATE(' ', key_) + 1)) - LOCATE(' ', key_)) AS subcategoria
	FROM
		(SELECT 
			b.key_
		FROM
			zabbix.trends a, zabbix.items b, zabbix.hosts c
		WHERE
			a.itemid = b.itemid
				AND b.hostid = c.hostid
				and b.key_ like '% % %'
				and b.hostid = $codhost "
				. $string_auxiliar .
			  " AND clock >= UNIX_TIMESTAMP(DATE_FORMAT(DATE_ADD(DATE_ADD(CURRENT_DATE, INTERVAL - DAY(CURRENT_DATE) + 1 DAY), INTERVAL - $mesesAnalisados MONTH), '%Y%m%d%H%i%s'))
				AND clock <= UNIX_TIMESTAMP(DATE_FORMAT(DATE_ADD(DATE_ADD(CURRENT_DATE, INTERVAL - DAY(CURRENT_DATE) + 1 DAY), INTERVAL - 1 SECOND), '%Y%m%d%H%i%s')) UNION SELECT 
			b.key_
		FROM
			zabbix.trends_uint a, zabbix.items b, zabbix.hosts c
		WHERE
			a.itemid = b.itemid
				AND b.hostid = c.hostid
				and b.key_ like '% % %'
				and b.hostid = $codhost"
				. $string_auxiliar .
			  " AND clock >= UNIX_TIMESTAMP(DATE_FORMAT(DATE_ADD(DATE_ADD(CURRENT_DATE, INTERVAL - DAY(CURRENT_DATE) + 1 DAY), INTERVAL - $mesesAnalisados MONTH), '%Y%m%d%H%i%s'))
				AND clock <= UNIX_TIMESTAMP(DATE_FORMAT(DATE_ADD(DATE_ADD(CURRENT_DATE, INTERVAL - DAY(CURRENT_DATE) + 1 DAY), INTERVAL - 1 SECOND), '%Y%m%d%H%i%s'))) x 
				where length(SUBSTRING(key_,
								LOCATE(' ', key_),
								LOCATE(' ', key_, (LOCATE(' ', key_) + 1)) - LOCATE(' ', key_)))>1 
				order by subcategoria";
	$rows = Select($query_possiveis_instancias);
	return $rows;
}


function buscaChavesZabbix($ArrayChavesCadastradas){
	global $codhost;
	global $seriesAnalisadas;	
	global $mesesAnalisados;
	$string_auxiliar = montaQueryAuxiliar($ArrayChavesCadastradas);
		
	$query_top5_keys = "select distinct substring(b.key_,
						  LOCATE(' ', b.key_),
						  LOCATE(' ', b.key_, (LOCATE(' ', b.key_) + 1)) - LOCATE(' ', b.key_)) as subcategoria	
				from zabbix.trends a,zabbix.items b, zabbix.hosts c
				where
				a.itemid = b.itemid
				and b.hostid = c.hostid
				and b.hostid = ". $codhost .
				$string_auxiliar . 
				"and clock >=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY),interval -$mesesAnalisados MONTH), '%Y%m%d%H%i%s')) 
				and clock <=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY), interval -1 second), '%Y%m%d%H%i%s')) 
				union 
				select distinct substring(b.key_,
						  LOCATE(' ', b.key_),
						  LOCATE(' ', b.key_, (LOCATE(' ', b.key_) + 1)) - LOCATE(' ', b.key_)) as subcategoria
				from zabbix.trends_uint a,zabbix.items b, zabbix.hosts c
				where
				a.itemid = b.itemid
				and b.hostid = c.hostid
				and b.hostid = ". $codhost .
				$string_auxiliar . 
				"and clock >=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY),interval -$mesesAnalisados MONTH), '%Y%m%d%H%i%s')) 
				and clock <=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY), interval -1 second), '%Y%m%d%H%i%s')) ";
	$rows = Select($query_top5_keys);
	return $rows;
}


function buscaQtdEventos($rows){
	global $codhost;
	global $mesesAnalisadosEventos;
	$string_auxiliar = montaQueryAuxiliar($rows);
	
	$query_eventos = "select count(*) quantidade
					from zabbix.items b
					left join zabbix.hosts h on b.hostid = h.hostid
					left join zabbix.functions f on b.itemid = f.itemid
					left join zabbix.triggers t on f.triggerid = t.triggerid
					left join zabbix.events e on  f.triggerid = e.objectid 
					where h.hostid = $codhost"
					. $string_auxiliar .
					"and clock >=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY),interval -$mesesAnalisadosEventos MONTH), '%Y%m%d%H%i%s')) 
					and clock <=  UNIX_TIMESTAMP(DATE_FORMAT(date_add(date_add(CURRENT_DATE,interval -DAY(CURRENT_DATE)+1 DAY), interval -1 second), '%Y%m%d%H%i%s'))
					and t.status = 0
					and b.status = 0";
	$rows = Select($query_eventos);
	foreach ($rows as $evento){
		return $evento["quantidade"];
	}
}

function processaDatas($ArrayValoresChave){
	global $mesesAnalisados;
	$categories = Array();
	$meses_ano = array('', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez');
	
	foreach($ArrayValoresChave as $item){
		if(count($categories) < $mesesAnalisados){
			$ano = substr($item['data'],0,4);
			$mes = $meses_ano[(int)substr($item['data'],5,2)];
			$data_ajustada = $mes . '/' . $ano;		
			array_push($categories, $data_ajustada);
		}		
	}
	return $categories;
}

function processaMedias($ArrayValoresChave){
	$series = Array();
	$array_maxima['name'] = 'Maxima';
	$array_media['name'] = 'Media';
	$array_minima['name'] = 'Minima';
	foreach($ArrayValoresChave as $item){
		$array_maxima['data'][] = $item['maxima'];
		$array_media['data'][] = $item['media'];
		$array_minima['data'][] = $item['minima'];	
	}
	
	array_push($series,$array_maxima);
	array_push($series,$array_media);
	array_push($series,$array_minima);
	return $series;
}

function processaValoresMultiplos($Objeto,$ArrayValoresChave){
	$array_object['name'] = $Objeto;
	foreach($ArrayValoresChave as $item){
		$array_object['data'][] = $item['media'];
	}
	return $array_object;
}

function buscaRegexObjeto($input_str){
	$pattern = "/\[\S+\s+\S+\s+(\S+)+\]/";
	if (preg_match($pattern, $input_str, $matches_out))
		return $matches_out[1];
	else
		return null;
}

function buscaRegexInicioChave($input_str){
	$pattern = "/\[\S+\s+\S+\s+(\S+)+\]/";
	if (preg_match($pattern, $input_str, $matches_out))
		return $matches_out[1];
	else
		return null;
}


function buscaRegexSubCategoria($input_str){
	$pattern = "/\[\S+\s+(\S+)+\s+\S+\]/";
	
	if (preg_match($pattern, $input_str, $matches_out))
		return $matches_out[1];
	else
		return null;
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

function buscaCincoMaioresChaves($nome_instancia,$rows){
	global $codhost;
	global $seriesAnalisadas;	
	global $mesesAnalisados;
	$string_auxiliar = montaQueryAuxiliar($rows);
	
	if(!is_null($nome_instancia))
		$string_auxiliar .= " and b.key_ like '%". $nome_instancia ."%' ";
		
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
		if(strpos($chave["valor"], '%')){
			//inicia a variavel
			if(is_null($string_auxiliar))
				$string_auxiliar = " and (b.key_ like '" . $chave["valor"] . "'";
			else			
			//concatena a variavel
				$string_auxiliar = $string_auxiliar . " or b.key_ like '" . $chave["valor"] . "'";
		}
		//Se a chave nao possui objeto dinamico
		else{
			//inicia a variavel
			if(is_null($string_auxiliar))
				$string_auxiliar = " and (b.key_ = '" . $chave["valor"] . "'";
			else			
			//concatena a variavel
				$string_auxiliar = $string_auxiliar . " or b.key_ = '" . $chave["valor"] . "'";
		}					
	}
	if(!is_null($string_auxiliar))
		$string_auxiliar .= ") ";
	
	return $string_auxiliar;
}

function buscaChavesCadastradas($grafico_id){
	global $codhost;
	$query_chaves = 	"select distinct valor
						from view_grafico_host
						where host_id = $codhost 
						and grafico_id = $grafico_id";
	$rows = Select($query_chaves);
	return $rows;
}


function buscaGraficos($categoria_id){
	global $codhost;
	$query_graficos = 	"select distinct 	grafico_id,
											titulo, 
											subtitulo,
											json
						from view_grafico_host
						where host_id = $codhost
						and categoria_id = $categoria_id";					
	$rows = Select($query_graficos);
	return $rows;
}

function buscaSubCategorias($categoria_id){
	global $codhost;
	$query_subcategorias = "select distinct valor 
							from view_grafico_host 
							where host_id = $codhost 
							and categoria_id = $categoria_id";
	$rows = Select($query_subcategorias);
	return $rows;
}

function buscaCategorias(){
	global $codhost;
	$query_categorias = "select distinct categoria_id,
										 nome
						from view_grafico_host
						where host_id = $codhost
						order by ordem";	
	$rows = Select($query_categorias);	
	return $rows;
}

function buscaEmpresa(){
	global $codhost;
	$query_categorias = "select empresa_id,
								empresa_nome, 
								responsavel_nome, 
								responsavel_email, 
								host_id, 
								host_nome
						from view_hosts
						where host_id = $codhost";	
	$rows = Select($query_categorias);	
	return $rows[0];
}


function buscaSubCategoriasDinamicas($categoria_id){
	global $codhost;
	$query_subcategorias = "select substring(valor,
									LOCATE(' ', valor),
									LOCATE(' ', valor, (LOCATE(' ', valor) + 1)) - LOCATE(' ', valor)) as subcategoria 
									from view_grafico_host 
									where host_id = $codhost 
									and categoria_id = $categoria_id";	
	$rows = Select($query_subcategorias);	
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
	