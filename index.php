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
$trackingRelat = $_GET["tracking"];

$host = new stdClass;


if(is_null($codhost)){
    debug("<h1>Informe um codigo de hostname</h1>");
    exit();
}
else{
    $identificador = generateID();
    
    setDefault();
    processaGrafico();
    
    printHeader($host->headerPrincipal);
        
    //adiciona uma imagem com id e codhost    
    printTrackingIMG($identificador,$codhost,$trackingRelat);
    
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
            printCategoriaHeader($host->categoria[$x]);
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
            printSubCategoriaHeader($host->subcategoria[$x]);
            $old_subcategoria = $host->subcategoria[$x];
            $cria_header_subcategoria = false;
        }
        printBody($x,$host->ArrayJson[$x],$host->ArrayEventos[$x]);
    }
    printFooter($host->nomeEmpresa,$host->responsavel);
    
    registraEnvio($identificador);
}



/**
 * inicio
 *
 * Processa toda a logica e loops em cima das categorias, subcagegorias (quando tiver), graficos, chaves, chaves dinamicas (quando tive)
 * analisa se o grafico plotara apenas uma chave e 3 medias ou varias chaves e apenas suas medias.
 *
 * @param (type) (id) identificador do e-mail.
 */
function processaGrafico(){
    global $host;
    
    $dadosEmpresa = buscaEmpresa();
    $host->nomeEmpresa = $dadosEmpresa["empresa_nome"];
    $host->nome = $dadosEmpresa["host_nome"];
    $host->headerPrincipal =  'RESUMO DE PERFORMANCE DO SERVIDOR - ' . $dadosEmpresa["host_nome"];
    $host->responsavel = $dadosEmpresa["responsavel_nome"];
    $host->emailResponsavel = $dadosEmpresa["responsavel_email"];
    $host->ArrayJson = [];
    $host->ArrayEventos = [];
    $host->categoria = [];
    $host->subcategoria = [];
    
    
    /**
     * Categorias
     *
     * Processa pelas categorias, cada categoria tem uma ordem de execucao
     */
    $ArrayCategorias = buscaCategorias();
    foreach ($ArrayCategorias as $categoria){
        debug("<hr>");
        debugl("[Categoria]" . $categoria["nome"]);
            
            
        /**
         * SubCategoria/Instancia
         *
         * Verifica quais chaves possuem dois espacos em branco ou o caracter de percentual (%),
         * Caso retorne alguma chave o objeto pode ter uma subcagegoria/instancia.
         */    
        $ArrayPossiveisInstancias = buscaPossiveisInstancias($categoria["categoria_id"]);    
        if(is_null($ArrayPossiveisInstancias))
            $ArrayPossiveisInstancias[]["subcategoria"] = null;
    
    
        /**
         * SubCategorias/Instancias
         *
         * Processa cada subcategoria/instancia
         */
        foreach($ArrayPossiveisInstancias as $instancia){
            $nome_instancia = $instancia["subcategoria"];
            debugl("[SubCategoria]".$instancia["subcategoria"]);


            /**
             * Graficos da Categoria/subcategoria
             *
             * Busca os graficos
             */
            $ArrayGraficos = buscaGraficos($categoria["categoria_id"]);
            
            
            
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
                $json = str_replace('$titulo_grafico',$grafico["titulo"],$json);
                $json = str_replace('$subtitulo_grafico',$grafico["subtitulo"],$json);
                
                
                
                /**
                 * Busca as chaves cadastradas neste grafico
                 */
                $ArrayChavesCadastradas = buscaChavesCadastradas($grafico["grafico_id"]);
                foreach ($ArrayChavesCadastradas as $chave){
                debugl("[ChaveBusca]" . $chave["valor"]);    
                }    
                
                /**
                 * Busca apenas as maiores chaves nao apenas 5, dependendo do parametro.
                 * Nome da instancia pra filtrar apenas as chaves que possuem o nome da instancia tambem (se existir instancia)
                 */
                $ArrayCincoMaioresChaves = buscaCincoMaioresChaves($nome_instancia,$ArrayChavesCadastradas);
                $QtdEventos = buscaQtdEventos($ArrayChavesCadastradas);
                
                
                /**
                 * Dependendo do total de chaves retornadas, o grafico tera varias chaves ou apenas uma que entao mostrara min,med,max
                 */
                $total = count($ArrayCincoMaioresChaves);
                $series = Array();
                $categories = Array();
                
                
                /**
                 * Processa cada chave encontrada
                 */
                foreach ($ArrayCincoMaioresChaves as $chaveMaior){
                    debugl("[ChaveRetorno]" . $chaveMaior["Chave"]);
                    
                    
                    /**
                     * Busca o nome do objeto, no padrao [string string objeto]
                     */
                    $Objeto = buscaRegexObjeto($chaveMaior["Chave"]);
                    /*
                    $SubCategoria = buscaRegexSubCategoria($chaveMaior["Chave"]);
                    debugl("++++++" . $SubCategoria);
                    debugl("++++++" . $Objeto);
                    */
                    
                    
                    /**
                     * Para cada chave busca seus valores, medias e datas por mes.
                     */
                    $ArrayValoresChave = buscaValoresPorChave($chaveMaior["Chave"]);    

                    /**
                     * Ponto onde decide se a legenda sera min,med,max ou o nome de cada objeto plotado
                     */
                    if($total>1){
                        debugl("[TipoPlotagem]"."print apenas as medias");
                        array_push($series,processaValoresMultiplos($Objeto,$ArrayValoresChave));
                        //debugl(json_encode($series, JSON_NUMERIC_CHECK));
                    }else{
                        debugl("[TipoPlotagem]"."print min/med/max");
                        $series = processaMedias($ArrayValoresChave);
                        //debugl(json_encode($series, JSON_NUMERIC_CHECK));
                    }    
                    
                    /**
                     * Abrevia o array de datas de 01 para Jan por exemplo
                     */            
                    $categories    = processaDatas($ArrayValoresChave);            
                }
                
                /**
                 * Faz um replace em $categorias colocando as data abreviadas no json
                 */    
                 debugl("? json ? ".$json);
                $json = str_replace('"$categorias"',json_encode($categories, JSON_NUMERIC_CHECK),$json);
                debugl("? json ? ".$json);
                
                
                /**
                 * Faz um replace em $series colocando os nomes e valores de cada serie a ser plotada
                 */    
                $json = str_replace('"$series"',json_encode($series, JSON_NUMERIC_CHECK),$json);
                //debugl("? eventos ? ".$QtdEventos);
                
                /**
                 * Armazena tudo em um unico objeto
                 */    
                array_push($host->categoria,$categoria["nome"]);
                array_push($host->subcategoria,$instancia["subcategoria"]);
                array_push($host->ArrayJson,$json);
                array_push($host->ArrayEventos,$QtdEventos);            
            }
        }                    
    }    
}


/**
 * registraEnvio
 *
 * Insere no banco que o email foi enviado.
 *
 * @param (type) (id) identificador do e-mail.
 */
function registraEnvio($id){
    global $codhost, $host;
    $ip = $_GET["ip"];
    $user = $_GET["user"];
    $password = $_GET["password"];
    $schema = $_GET["schema"];
    
    $safe_string_host = base64_encode(serialize($host));
    
    $conexao =  mysqli_connect($ip,$user,$password,$schema);
    $result = mysqli_query($conexao, "INSERT INTO historico (id, host_id, object_json) values ($id, $codhost, '".$safe_string_host."')");
    mysqli_close($conexao);
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
    $ip = $_GET["ip"];
    $user = $_GET["user"];
    $password = $_GET["password"];
    $schema = $_GET["schema"];
    
    $conexao =  mysqli_connect($ip,$user,$password,$schema);
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }else{
        $cursor = mysqli_query($conexao, 'INSERT INTO historico () VALUES ()');
        $cursor = mysqli_query($conexao, 'ROLLBACK');
        $cursor = mysqli_query($conexao,"SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'zabbix_refatorado' AND TABLE_NAME   = 'historico'");
        $resultado = mysqli_fetch_all($cursor, MYSQLI_ASSOC);
        $id = $resultado[0]["AUTO_INCREMENT"];
        return $id;    
    }
    mysqli_close($conexao);
}

/**
 * setDefault
 *
 * Atribui valores padroes para as variaveis do grafico.
 *
 */
function setDefault(){
    global $mesesAnalisados,$mesesAnalisadosEventos,$seriesAnalisadas;
    
    if(is_null($mesesAnalisados))
        $mesesAnalisados = 3;    
    if(is_null($mesesAnalisadosEventos))
        $mesesAnalisadosEventos = 1;    
    if(is_null($seriesAnalisadas))
        $seriesAnalisadas = 5;
}


/**
 * buscaPossiveisInstancias
 *
 * Busca todas as chaves que tenham percentual (%) e busca na base do Zabbix um padrao de nomes para instancias/subcategorias.
 *
 * @param (type) (categoria_id) filtro para buscar apenas chaves da categoria.
 * @return (type) (rows) array com nomes de possiveis instancias.
 */
function buscaPossiveisInstancias($categoria_id){
    global $codhost;
    global $mesesAnalisados;
    
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
    $rows = Select($query_possiveis_instancias);
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
function buscaChavesZabbix($ArrayChavesCadastradas){
    global $codhost;
    global $mesesAnalisados;
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


/**
 * processaDatas
 *
 * Retorna em formado abreviado os meses processados no grafico.
 *
 * @param (type) (ArrayValoresChave) array com meses em valores decimais.
 * @param (type) (categories) array com meses em valores abreviados.
 */
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
 * Insere no banco que o email foi enviado.
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


/**
 * buscaRegexObjeto
 *
 * Busca string conforme pattern.
 * [string string objeto]
 *
 * @param (type) (input_str) string a ser analisada.
 * @return (type) ($matches_out[1]) string encontrada.
 */
function buscaRegexObjeto($input_str){
    $pattern = "/\[\S+\s+\S+\s+(\S+)+\]/";
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
function buscaChavesCadastradas($grafico_id){
    global $codhost;
    $query_chaves =     "select distinct valor
                        from view_grafico_host
                        where host_id = $codhost 
                        and grafico_id = $grafico_id";
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
function buscaGraficos($categoria_id){
    global $codhost;
    $query_graficos =     "select distinct     grafico_id,
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
 * registraEnvio
 *
 * Insere no banco que o email foi enviado.
 *
 * @param (type) (id) identificador do e-mail.
 */
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


/**
 * registraEnvio
 *
 * Insere no banco que o email foi enviado.
 *
 * @param (type) (id) identificador do e-mail.
 */
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


/**
 * registraEnvio
 *
 * Verifica se foi passado o parametro debug=true, insere a string sem quebra linha no final.
 *
 * @param (type) (text) texto para inserir no html.
 */
function debug($text){
    $debug = $_GET["debug"];
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
    $debug = $_GET["debug"];
    if($debug === strtolower("true")){
        echo($text."</br>");
    }
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