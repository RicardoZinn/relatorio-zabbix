<?php
header('Content-type: text/html; charset=ISO-8859-1');

// Allow access from anywhere. Can be domains or * (any)

header('Access-Control-Allow-Origin: *');

// Allow these methods of data retrieval

header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

// Allow these header types

header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

// configuracoes do banco de dados

require_once ('database.inc.php');

include "fheader.php";

include "fbody.php";

include "ffooter.php";

$codhost = $_GET["codhost"];
$emailid = $_GET["emailid"];
$trackingRelat = $_GET["tracking"];
$host = new stdClass;

if (is_null($codhost)) {
    echo ("<h1>Informe um codigo de hostname</h1>");
    exit();
}
else
if (is_null($emailid)) {
    echo ("<h1>Informe um codigo de email enviado</h1>");
    exit();
}
else {
    $resultado = RecuperaHistoricoGrafico($emailid, $codhost);
    $host = unserialize(base64_decode($resultado));
    if (is_null($host->nome)) {
        echo ("<h1>Não encontramos o registro do e-mail ($emailid) para o host ($codhost) </br>:(");
        exit();
    }

    printHeader($host->nome);
    $total_categorias = count($host->categoria);
    $total_subcategoria = count($host->subcategoria);
    $total_json = count($host->ArrayJson);
    $total_eventos = count($host->ArrayEventos);
    $cria_header_categoria = false;
    $old_categoria = null;
    $cria_header_subcategoria = false;
    $old_subcategoria = null;
    for ($x = 0; $x < $total_categorias; $x++) {
        if (strcmp($old_categoria, $host->categoria[$x]) == 0) $cria_header_categoria = false;
        else $cria_header_categoria = true;
        if ($cria_header_categoria) {
            printCategoriaHeader($host->categoria[$x]);
            $old_categoria = $host->categoria[$x];
            $cria_header_categoria = false;
        }

        if (is_null($host->subcategoria[$x])) $cria_header_subcategoria = false;
        if (strcmp($old_subcategoria, $host->subcategoria[$x]) == 0) $cria_header_subcategoria = false;
        else $cria_header_subcategoria = true;
        if ($cria_header_subcategoria) {
            printSubCategoriaHeader($host->subcategoria[$x]);
            $old_subcategoria = $host->subcategoria[$x];
            $cria_header_subcategoria = false;
        }

        printBody($x, $host->ArrayJson[$x], $host->ArrayEventos[$x]);
    }

    printFooter($host->nomeEmpresa, $host->responsavel);
    printTrackingIMG($emailid, $codhost, $trackingRelat);
}

function UnserializeObjeto($emailid, $host_id)
{
    $obj = RecuperaHistoricoGrafico($emailid, $host_id);
    echo "obj_string " . $obj;
    echo "</br>";
    echo "</br>";
    $host = unserialize($obj);
    return $host;
}

function RecuperaHistoricoGrafico($emailid, $host_id)
{
    $query_grafico = "select     object_json
                    from historico 
                    where id = $emailid 
                    and host_id = $host_id ";
    $rows = Select($query_grafico);
    return $rows[0]["object_json"];
}

/**
 * Select
 *
 * Processa query retornando um array associativo.
 *
 * @param (type) (query) querystring.
 * @param (type) (resultado) array associativo.
 */

function Select($query)
{
    $ip = $_GET["ip"];
    $user = $_GET["user"];
    $password = $_GET["password"];
    $schema = $_GET["schema"];
    $conexao = mysqli_connect("$ip", "$user", "$password", "$schema");
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
    else {
        $cursor = mysqli_query($conexao, $query);
        $resultado = mysqli_fetch_all($cursor, MYSQLI_ASSOC);
        return $resultado;
    }
}

?>