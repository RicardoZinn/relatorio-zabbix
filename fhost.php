<?php 
header('Content-type: text/html; charset=ISO-8859-1');

// Allow access from anywhere. Can be domains or * (any)
header('Access-Control-Allow-Origin: *');

// Allow these methods of data retrieval
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

// Allow these header types
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

//configuracoes do banco de dados
include "database_config.php";
include "fheader.php";
include "fbody.php";
include "ffooter.php";

function processaRelatorioHost($id,$host){
	$html = "";
    $html .= getHeaderHTML($host->headerPrincipal);
        
	$codhost = $host->codhost;	
	$tracking = isTracking();
    
    
    $total_categorias = count($host->categoria);
    $total_subcategoria = count($host->subcategoria);
    $total_json = count($host->ArrayJson);
    $total_eventos = count($host->ArrayEventos);

    
    debugl("total_categorias-> $total_categorias");
    debugl("total_subcategoria -> $total_subcategoria");
    debugl("total_json -> $total_json");
    debugl("total_eventos -> $total_eventos");
    
    //simula 2 instancias
    //$host->subcategoria[2] = 'produtttt';
    
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
            $host->categoria[$x] = $host->categoria[$x];
            $html .= getCategoriaHeaderHTML($host->categoria[$x]);
            //echo "[CRIAR HEADER]". $host->categoria[$x] . "</br>";
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
            //echo "[CRIAR SUBHEADER]". $host->subcategoria[$x];
            $host->subcategoria[$x] = $host->subcategoria[$x];
            $html .= getSubCategoriaHeaderHTML($host->subcategoria[$x]);
            $old_subcategoria = $host->subcategoria[$x];
            $cria_header_subcategoria = false;
        }
        //$html .= getBodyHTML($x,$host->ArrayJson[$x],$host->ArrayEventos[$x]);
        $html .= getBodyHTML($x,$host->images[$x],$host->ArrayEventos[$x]);
    }
    $html .= getFooterHTML($host->nomeEmpresa,$host->responsavel);
	
	//adiciona uma imagem com id    
    $html .= getTrackingHTML($id,$tracking);
	return $html;	
}




/**
 * inicio
 *
 * Processa toda a logica e loops em cima das categorias, subcagegorias (quando tiver), graficos, chaves, chaves dinamicas (quando tive)
 * analisa se o grafico plotara apenas uma chave e 3 medias ou varias chaves e apenas suas medias.
 *
 * @param (type) (id) identificador do e-mail.
 */
function createHost($codhost){
	$host = new stdClass;
    $dadosEmpresa = buscaEmpresa($codhost);
	$host->codhost = $codhost;
    $host->nomeEmpresa = $dadosEmpresa["empresa_nome"];
    $host->nome = $dadosEmpresa["host_nome"];
    $host->headerPrincipal =  'RESUMO DE PERFORMANCE DO SERVIDOR - ' . $dadosEmpresa["host_nome"];
    $host->responsavel = $dadosEmpresa["responsavel_nome"];
    $host->emailResponsavel = $dadosEmpresa["responsavel_email"];
    $host->CopiasControladas = getCopiasControladas($codhost);
    $host->ArrayJson = [];
    $host->ArrayEventos = [];
    $host->categoria = [];
    $host->subcategoria = [];
	$host->images = [];
    
	$host->numMaxPeriodAnalise = getMaxPeriodAnalise();
	$host->numMaxObjetosGrafico = getMaxObjetosGrafico();
	$host->numeroMesesEventos = numMaxPeriodoEvento();

    
    /**
     * Categorias
     *
     * Processa pelas categorias, cada categoria tem uma ordem de execucao
     */
    $ArrayCategorias = buscaCategorias($codhost);
    foreach ($ArrayCategorias as $categoria){
        debug("<hr>");
        debugl("[Categoria]" . $categoria["nome"]);
            
            
        /**
         * SubCategoria/Instancia
         *
         * Verifica quais chaves possuem dois espacos em branco ou o caracter de percentual (%),
         * Caso retorne alguma chave o objeto pode ter uma subcagegoria/instancia.
         */    
        $ArrayPossiveisInstancias = buscaPossiveisInstancias($codhost,$categoria["categoria_id"],$host->numMaxPeriodAnalise);    
        if(is_null($ArrayPossiveisInstancias))
            $ArrayPossiveisInstancias[]["subcategoria"] = null;
    
    
        /**
         * SubCategorias/Instancias
         *
         * Processa cada subcategoria/instancia
         */
		 debugl("Instancias: " .count($ArrayPossiveisInstancias));
        foreach($ArrayPossiveisInstancias as $instancia){
            $nome_instancia = $instancia["subcategoria"];
            debugl("[SubCategoria]".$instancia["subcategoria"]);


            /**
             * Graficos da Categoria/subcategoria
             *
             * Busca os graficos
             */
            $ArrayGraficos = buscaGraficos($codhost,$categoria["categoria_id"]);
            
            
            
            /**
             * Graficos da Categoria/subcategoria
             *
             * Processa cada grafico
             */
            foreach ($ArrayGraficos as $grafico){
                debug("<hr>");
                
                
                /**
                 * Altera o json com o nome do titulo e subtitulo do grafico
                 */                
                $json = $grafico["json"];
                
                
                
                
                /**
                 * Busca as chaves cadastradas neste grafico
                 */
                $ArrayChavesCadastradas = buscaChavesCadastradas($codhost,$grafico["grafico_id"]);
                foreach ($ArrayChavesCadastradas as $chave){
                debugl("[ChaveBusca]" . $chave["valor"]);    
                }    
                
                /**
                 * Busca apenas as maiores chaves nao apenas 5, dependendo do parametro.
                 * Nome da instancia pra filtrar apenas as chaves que possuem o nome da instancia tambem (se existir instancia)
                 */
                $ArrayCincoMaioresChaves = buscaCincoMaioresChaves($codhost,$nome_instancia,$ArrayChavesCadastradas,$host->numMaxObjetosGrafico,$host->numMaxPeriodAnalise);
                $QtdEventos = buscaQtdEventos($codhost,$ArrayChavesCadastradas,$host->numMaxPeriodAnalise);
                
                
                /**
                 * Dependendo do total de chaves retornadas, o grafico tera varias chaves ou apenas uma que entao mostrara min,med,max
                 */
                $total = count($ArrayCincoMaioresChaves);
                $series = Array();
                $categories = Array();
                
                
                /**
                 * Processa cada chave encontrada
                 */
				 $alterou_titulo = false;
                foreach ($ArrayCincoMaioresChaves as $chaveMaior){
                    debugl("[ChaveRetorno]" . $chaveMaior["Chave"]);
                	//retorna a legenda da primeira chave que encontrar baseada em uma das chaves com maior valor
					//echo "Teste de legenda...para " . $chaveMaior["Chave"];
					
					$legenda = null;
					$legendaChave = null;
					$legenda = buscaLegendaChaveCadastrada($ArrayChavesCadastradas,$chaveMaior["Chave"]);
					$legendaChave = buscaRegexObjetoNovoRegex2($chaveMaior["Chave"],$legenda);
					if(is_null($legendaChave))
						$legendaChave = $chaveMaior["Chave"];
					
					if($alterou_titulo === false){
						$titulo_grafico = $grafico["titulo"];
						$subtitulo_grafico = $grafico["subtitulo"];
						$titulo_grafico = buscaRegexObjetoNovoRegex2($chaveMaior["Chave"],$grafico["titulo"]);
						$subtitulo_grafico = buscaRegexObjetoNovoRegex2($chaveMaior["Chave"],$grafico["subtitulo"]);
						$json = str_replace('$titulo_grafico',$titulo_grafico,$json);
						$json = str_replace('$subtitulo_grafico',$subtitulo_grafico,$json);
						$alterou_titulo = true;
					}
					
                    /**
                     * Para cada chave busca seus valores, medias e datas por mes.
                     */
                    $ArrayValoresChave = buscaValoresPorChave($codhost,$chaveMaior["Chave"],$host->numMaxPeriodAnalise);    

                    /**
                     * Ponto onde decide se a legenda sera min,med,max ou o nome de cada legendaChave plotado
                     */
                    if($total>1){
                        debugl("[TipoPlotagem]"."print apenas as medias");
							array_push($series,processaValoresMultiplos($legendaChave,$ArrayValoresChave));
						/**
						* apresentar novo nome para o objeto
						**/
						
                        //debugl(json_encode($series, JSON_NUMERIC_CHECK));
                    }else{
                        debugl("[TipoPlotagem]"."print min/med/max");
                        $series = processaMedias($ArrayValoresChave);
                        //debugl(json_encode($series, JSON_NUMERIC_CHECK));
                    }    
                    
                    /**
                     * Abrevia o array de datas de 01 para Jan por exemplo
                     */            
                    $categories = processaDatas($ArrayValoresChave,$host->numMaxPeriodAnalise);            
                }
                
                /**
                 * Faz um replace em $categorias colocando as data abreviadas no json
                 */    
                debugl("Json limpo: ".$json);
                $json = str_replace('"$categorias"',json_encode($categories, JSON_NUMERIC_CHECK),$json);
                debugl("Json \$categorias: ".$json);
                
                
                /**
                 * Faz um replace em $series colocando os nomes e valores de cada serie a ser plotada
                 */    
                $json = str_replace('"$series"',json_encode($series, JSON_NUMERIC_CHECK),$json);
				debugl("Json \$series: ".$json);
                //debugl("? eventos ? ".$QtdEventos);
                
				//criando imagens locais (bug gmail)
				
				$url_encoded = createWebServiceURL($json);
				debugl("\$url_encoded: $url_encoded");
				$imagem = createLocalImage($url_encoded);
				
				
				/* imagem em base64
				*
				$imagem = createBase64Image($url_encoded);
				*
				*/
				
                /**
                 * Armazena tudo em um unico objeto
                 */    
                array_push($host->categoria,$categoria["nome"]);
                array_push($host->subcategoria,$instancia["subcategoria"]);
                array_push($host->ArrayJson,$json);
				array_push($host->images,$imagem);
                array_push($host->ArrayEventos,$QtdEventos);            
            }
        }                    
    }   
	return $host;
}

function getCopiasControladas($codhost){
	$query = "select d.nome,d.email
			from empresa_destinatario ed
			inner join empresa e on (e.id = ed.empresa_id)
			inner join destinatario d on (ed.destinatario_id = d.id)
			inner join empresa_host eh on (eh.empresa_id = e.id)
			where e.responsavel <> d.id
			and eh.host_id = $codhost";

	$rows = Select($query); 
	return $rows;
}


function getNomeLegenda($string){
	$queryLegenda = "select nome_legenda from chave where valor = \"$string\"";
    $rows = Select($queryLegenda);  
	return $rows[0]["nome_legenda"];
}



/**
 * createWebServiceURL
 *
 * Recebe um json contendo as opcoes do grafico, datasource, etc.
 *
 * @param (type) (url) url contendo imagem.
 * @return (type) (urlWebservice) url do webservice.
 */
function createWebServiceURL($json){
	$urlWebservice = 'http://export.highcharts.com/?content=options&options='. utf8_decode(urlencode($json)) .'&type=image/png&width=800&scale=&constr=Chart';
	return $urlWebservice;
}


/**
 * createLocalImage
 *
 * Recebe uma url contendo uma imagem cria a imagem no disco e retorna o caminho/url.
 *
 * @param (type) (url) url contendo imagem.
 * @return (type) retorna url com endereço da imagem.
 */
function createLocalImage($url){
	$extension = ".png";
	$unique    = uniqid(rand(), true);
	$filename  = $unique . $extension;
	$path      = "images/";
	$url_path  = "http://helpdesk.nvl.inf.br/painel/report/" . $path;
	
	exec("php createImageFromUrl.php \"$url\" $unique > /dev/null 2>/dev/null &");
	/*
	$img = @imagecreatefrompng($url);
	imagepng($img, $path . $filename);
	*/
	return $url_path . $filename;
}

/**
 * createBase64Image
 *
 * Recebe uma url contendo uma imagem e retorna a imagem na base64.
 *
 * @param (type) (url) url contendo imagem.
 * @return (type) retorna imagem na base64.
 */
function createBase64Image($url){
	$img = file_get_contents($url);
	$type = "png";
	$imgbase64 = base64_encode($img);
	return "data:image/" . $type . ";base64," . $imgbase64; 
}


/**
 * registraGeracaoHTML
 *
 * Insere no banco que o email foi enviado.
 *
 * @param (type) (id) identificador do e-mail.
 */
function registraGeracaoHTML($id,$host){
	$codhost = $host->codhost;
    $safe_string_host = base64_encode(serialize($host));
    $query = "INSERT INTO historico (id, host_id, phpbase64) values ($id, $codhost, '".$safe_string_host."')";
	return Update($query);
}

/**
 * generateID
 *
 * Faz um fake-insert incrementando a sequence no banco e logo em seguida recuperando a mesma.
 *
 * @return (type) (id) identificador do e-mail.
 */
//retorna nova sequence no mysql
function generateID(){
	$config = parse_ini_file('database.ini'); 
    $conexao =  mysqli_connect($config['ip'],$config['username'],$config['password'],$config['dbname']);
	$query = "SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = " . " \"" . $config['dbname'] . "\" AND TABLE_NAME = 'historico' ";
	$result = Select($query);
	return $result[0]["AUTO_INCREMENT"];
}


function getMaxPeriodAnalise(){
	return isset($_GET["maxperiodanalise"]) ? $_GET["maxperiodanalise"] : 3;
}
function getMaxObjetosGrafico(){
	return isset($_GET["maxobj"]) ? $_GET["maxobj"] : 5;
}
function numMaxPeriodoEvento(){
	return isset($_GET["maxobj"]) ? $_GET["maxobj"] : 1;
}

function isTracking(){
	return isset($_GET["tracking"]) ? $_GET["tracking"] : null;
}


/**
 * buscaPossiveisInstancias
 *
 * Busca todas as chaves que tenham percentual (%) e busca na base do Zabbix um padrao de nomes para instancias/subcategorias.
 *
 * @param (type) (categoria_id) filtro para buscar apenas chaves da categoria.
 * @return (type) (rows) array com nomes de possiveis instancias.
 */
function buscaPossiveisInstancias($codhost,$categoria_id,$mesesAnalisados){
    /**
     * Verifica quais chaves possuem dois espacos em branco ou o caracter de percentual (%),
     * Caso retorne alguma chave o objeto pode ter uma subcagegoria/instancia.
     */    
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
	debugl("\$query_possiveis_instancias: $query_possiveis_instancias");
    $rows = Select($query_possiveis_instancias);
	
	//gambiarra para atender nova solicitacao do gomes
	if(count($rows)<1)
		return null;
    return $rows;
}


/**
 * buscaChavesZabbix
 *
 * Recebe um array com todas as chaves cadastradas e compara quais existem na base do zabbix, retornando apenas as chaves em ambas as bases.
 *
 * @param (type) (ArrayChavesCadastradas) array com todas as chaves cadastradas.
 * @return (type) (rows) array com chaves unicas cadastradas em ambas as bases.
 */
function buscaChavesZabbix($codhost, $ArrayChavesCadastradas,$mesesAnalisados){
    $string_auxiliar = montaQueryAuxiliar($ArrayChavesCadastradas);
        
    $query_chave_zabbix = "select distinct substring(b.key_,
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
    $rows = Select($query_chave_zabbix);
    return $rows;
}


/**
 * buscaQtdEventos
 *
 * Conta todos os eventos disparados pelas chaves do grafico no periodo informado.
 *
 * @param (type) (rows) array de chaves.
 * @return (type) ($evento["quantidade"]) quantidade de eventos disparados pelas chaves no periodo.
 */
function buscaQtdEventos($codhost,$rows,$mesesAnalisadosEventos){
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


/**
 * processaDatas
 *
 * Retorna em formado abreviado os meses processados no grafico.
 *
 * @param (type) (ArrayValoresChave) array com meses em valores decimais.
 * @param (type) (categories) array com meses em valores abreviados.
 */
function processaDatas($ArrayValoresChave,$mesesAnalisados){
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


/**
 * processaMedias
 *
 * Recebe um array com valores de medias, cria um array externo e coloca os valores em sequencia, separando media, maxima, minima.
 * Usado quando o grafico possui apenas uma chave em ambas as bases.
 *
 * @param (type) (ArrayValoresChave) array com tres medias por indice ou por mes.
 * @return (type) (series) array multidimensional onde o nome de cada array sera a legenda do grafico, neste caso o tipo de media.
 */
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


/**
 * processaValoresMultiplos
 *
 * 
 *
 * @param (type) (Objeto) nome do objeto, sera uma da(s) legenda(s).
 * @param (type) (ArrayValoresChave) array com os valores medianos de cada mes.
 * @param (type) (array_object) array multidimensional onde o nome de cada array sera a legenda do grafico, neste caso o nome do objeto.
 */
function processaValoresMultiplos($Objeto,$ArrayValoresChave){
    $array_object['name'] = $Objeto;
    foreach($ArrayValoresChave as $item){
        $array_object['data'][] = $item['media'];
    }
    return $array_object;
}

function buscaLegendaChaveCadastrada($arrayCadastrada,$chaveZabbix){
	foreach($arrayCadastrada as $chaveCadastrada){
		$tmppattern  = '';
		//altera os caracteres especiais para . (ponto)
		$tmppattern = preg_replace("/([\.\[\]])/i", ".", $chaveCadastrada["valor"]);
		//altera o caractere % para .* (qualquer string e inumeras vezes)
		$tmppattern = preg_replace("/(\%)/i", ".*", $tmppattern);
		$tmppattern = "/" . $tmppattern . "/i";
		//se a string contem o regex montado entao retorna a legenda
		if(preg_match($tmppattern,$chaveZabbix, $matches_out)){
			return $chaveCadastrada["legenda_chave"];
		}
	}
	return null;
}



function buscaRegexObjetoNovoRegex2($input_str,$string_replace){
	if(is_null($string_replace))
		return $input_str;
	
	$pattern = '/([^\w\/]+)/i';
	preg_match_all($pattern, $input_str, $matches_out);
	
	$pattern = '/';
	for($i=0; $i<count($matches_out[0]); $i++){
		$pattern .= '([\w\/]+)[^\w\/]+';
    }
	$pattern .= '/i';
	
	if (preg_match($pattern, $input_str, $matches_out))
		return preg_replace($pattern, $string_replace, $input_str);

	return $string_replace;
}


/**
* $string_default = system.cpu.load[all,avg5]
* $string_replace = Objeto - $3
* $regex_pattern = /(.*)(\,)(.*)(\])/i
* $resultado = Objeto - avg5
**/
function buscaRegexObjetoNovoRegex($input_str,$string_replace){
	if(is_null($string_replace))
		return $input_str;
	
	$pattern = '/.*\[(\w*)[ ,](\w*)[ ,](\w*)[ ,](\w*)[ ,](\w*)\]/i';
	if (preg_match($pattern, $input_str, $matches_out))
		return preg_replace($pattern, $string_replace, $input_str);
	
	$pattern = '/.*\[(\w*)[ ,](\w*)[ ,](\w*)[ ,](\w*)\]/i';
	if (preg_match($pattern, $input_str, $matches_out))
		return preg_replace($pattern, $string_replace, $input_str);
	
	$pattern = '/.*\[(\w*)[ ,](\w*)[ ,](\w*)\]/i';
	if (preg_match($pattern, $input_str, $matches_out))
		return preg_replace($pattern, $string_replace, $input_str);
	
	$pattern = '/.*\[(\w*)[ ,](\w*)\]/i';
	if (preg_match($pattern, $input_str, $matches_out))
		return preg_replace($pattern, $string_replace, $input_str);
	
	$pattern = '/.*\[(\w*)\]/i';
	if (preg_match($pattern, $input_str, $matches_out))
		return preg_replace($pattern, $string_replace, $input_str);

	return $string_replace;
}

/**
 * buscaRegexObjetoGOMES
 *
 * Busca string conforme pattern.
 * [string string objeto]
 *
 * @param (type) (input_str) string a ser analisada.
 * @return (type) ($matches_out[1]) string encontrada.
 */
function buscaRegexObjetoGOMES($input_str){
    //padrao banco de dados [string string nome]
	$pattern = "/\[\S+\s+\S+\s+(\S+)+\]/";
    if (preg_match($pattern, $input_str, $matches_out))
        return $matches_out[1];
	else
		return null;
}

/**
 * buscaRegexObjetoZabbix
 *
 * Busca string conforme pattern.
 * [string string objeto]
 *
 * @param (type) (input_str) string a ser analisada.
 * @return (type) ($matches_out[1]) string encontrada.
 */
function buscaRegexObjetoZabbix($input_str){
    //padrao banco de dados [string string nome]
	$pattern = "/\[(\S+)+,/";
    if (preg_match($pattern, $input_str, $matches_out))
        return $matches_out[1];
	else
		return null;
}



/**
 * buscaRegexInicioChave
 *
 * Busca o inicio da chave, antecedendo do caracter "[" e seguido de espaco em branco ate o final com "]".
 * [objeto string string]
 *
 * @param (type) (input_str) string a ser analisada.
 * @return (type) ($matches_out[1]) string encontrada.
 */
function buscaRegexInicioChave($input_str){
    $pattern = "/\[\S+\s+\S+\s+(\S+)+\]/";
    if (preg_match($pattern, $input_str, $matches_out))
        return $matches_out[1];
    else
        return null;
}


/**
 * buscaRegexSubCategoria
 *
 * Insere no banco que o email foi enviado.
 * [string objeto string]
 *
 * @param (type) (input_str) string a ser analisada.
 * @return (type) ($matches_out[1]) string encontrada.
 */
function buscaRegexSubCategoria($input_str){
    $pattern = "/\[\S+\s+(\S+)+\s+\S+\]/";
    
    if (preg_match($pattern, $input_str, $matches_out))
        return $matches_out[1];
    else
        return null;
}


/**
 * buscaValoresPorChave
 *
 * Busca na base do Zabbix os valores para a chave informada no periodo de meses a analisar para tras.
 *
 * @param (type) (oneOfTop5keys) chave para consulta de valores.
 * @return (type) (rows) array com minima, media, maxima, data.
 */
function buscaValoresPorChave($codhost,$oneOfTop5keys,$mesesAnalisados){
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
                    select     round(min(value_min),2) as minima,
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


/**
 * buscaCincoMaioresChaves
 *
 * Busca as chaves com maiores valores, levando em consideracao parametros de meses e quantidade de chaves para retorno.
 *
 * @param (type) (nome_instancia) nome da instancia para filtrar a query e chave.
 * @param (type) (rows) array com todas as chaves.
 * @return (type) (rows) array com as 5 chaves que possuem o maior valor no periodo analisado.
 */
function buscaCincoMaioresChaves($codhost, $nome_instancia, $rows,$seriesAnalisadas,$mesesAnalisados){
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
                select     round(sum(value_max)/count(*),2) as media,b.key_,
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


/**
 * montaQueryAuxiliar
 *
 * Recebe varias chaves e coloca cada uma dentro de varias clausulas or.. or.. or..
 *
 * @param (type) (rows) array de chaves.
 * @return (type) (string_auxiliar) string para concatenar na query e filtrar as chaves.
 */
function montaQueryAuxiliar($rows){
	$string_auxiliar = null;
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


/**
 * buscaChavesCadastradas
 *
 * Busca todas as chaves cadastradas para este grafico.
 *
 * @param (type) (grafico_id) filtro do grafico.
 * @return (type) (rows) array com chaves cadastradas.
 */
function buscaChavesCadastradas($codhost, $grafico_id){
    $query_chaves =     "select distinct 	chave_id,
											valor,
											legenda_chave
                        from view_grafico_host
                        where host_id = $codhost 
                        and grafico_id = $grafico_id
						order by chave_id";
    $rows = Select($query_chaves);
    return $rows;
}


/**
 * buscaGraficos
 *
 * Busca todos os graficos cadastrados para a categoria e host.
 *
 * @param (type) (categoria_id) identificador do e-mail.
 * @return (type) (rows) array de graficos, titulos, subtitulos.
 */
function buscaGraficos($codhost, $categoria_id){
    $query_graficos =     "select distinct  grafico_id,
                                            titulo, 
                                            subtitulo,
                                            json
                        from view_grafico_host
                        where host_id = $codhost
                        and categoria_id = $categoria_id";                    
    $rows = Select($query_graficos);
    return $rows;
}



/**
 * buscaCategorias
 *
 * Insere no banco que o email foi enviado.
 *
 * @param (type) (id) identificador do e-mail.
 */
function buscaCategorias($codhost){
    $query_categorias = "select distinct categoria_id,
                                         nome
                        from view_grafico_host
                        where host_id = $codhost
                        order by ordem";    
    $rows = Select($query_categorias);    
    return $rows;
}


/**
 * buscaEmpresa
 *
 * Insere no banco que o email foi enviado.
 *
 * @param (type) (id) identificador do e-mail.
 */
function buscaEmpresa($codhost){
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


/**
 * debug
 *
 * Verifica se foi passado o parametro debug=true, insere a string sem quebra linha no final.
 *
 * @param (type) (text) texto para inserir no html.
 */
function debug($text){
	$debug = isset($_GET["debug"]) ? $_GET["debug"] : null;
    if($debug === strtolower("true")){
        echo($text);
    }
}

/**
 * debugl
 *
 * Verifica se foi passado o parametro debug=true, insere a string com um quebra linha no final.
 *
 * @param (type) (text) texto para inserir no html.
 */
function debugl($text){
	$debug = isset($_GET["debug"]) ? $_GET["debug"] : null;
    if($debug === strtolower("true")){
        echo($text."</br>");
    }
}

function RecuperaHistoricoGrafico($mailid)
{
    $query_grafico = "select phpbase64
                    from historico 
                    where id = $mailid 
					";
    $rows = Select($query_grafico);
    return $rows[0]["phpbase64"];
}

/**
 * Select
 *
 * Processa query retornando um array associativo.
 *
 * @param (type) (query) querystring.
 * @param (type) (resultado) array associativo.
 */
function Select($query){
	$config = parse_ini_file('database.ini'); 
    $conexao =  mysqli_connect($config['ip'],$config['username'],$config['password'],$config['dbname']);
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }else{
        $cursor = mysqli_query($conexao, $query);
        $resultado = mysqli_fetch_all($cursor, MYSQLI_ASSOC);
		mysqli_close($conexao);
        return $resultado;    
    }
}


/**
 * Update
 *
 * Processa query retornando um array associativo.
 *
 * @param (type) (query) querystring.
 * @param (type) (resultado) array associativo.
 */
function Update($query){
	$config = parse_ini_file('database.ini'); 
    $conexao =  mysqli_connect($config['ip'],$config['username'],$config['password'],$config['dbname']);
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }else{
        $cursor = mysqli_query($conexao, $query);
        $resultado = mysqli_fetch_all($cursor, MYSQLI_ASSOC);
		$total = mysqli_affected_rows($conexao);
		mysqli_close($conexao);
        return $total;    
    }
}

function setDataEnvio($mailid){
	$query = "UPDATE historico set data_envio = CURRENT_TIMESTAMP
				where id = $mailid ";	
	return Update($query);
}


function getObjectsToSend(){
	$query = "select id,
					phpbase64 
					from historico 
					where data_geracao is not null
					and data_revisao is not null
					and data_envio is null ";	
	$rows = Select($query);
	return $rows;
}


?>