<?php 

/**
 * printTrackingIMG
 *
 * Insere uma imagem 1px x 1px que quando acessada passa os mesmos parametros para a pagina tracking.php
 *
 * @param (type) (id) identificador unico do email.
 * @param (type) (codhost) identificador do codigo do host.
 */
function printTrackingIMG($id,$codhost,$trackingRelat){
	if(strcmp($trackingRelat,"true") == 0){
		echo "<img src=\"http://192.168.2.183/painel/doriclaudino/tracking.php?id=$id&codhost=$codhost\" alt=\"\" width=\"1\" height=\"1\" border=\"0\" />";
	}
}

/**
 * printSubCategoriaHeader
 *
 * Insere o começo do html e o header principal com o nome do host
 *
 * @param (type) (header_string) texto exibido no header principal.
 */
function printSubCategoriaHeader($header_string){
	$html = "";	
	$html  .= '    <span style="display:none!important;font-size:1px;color:transparent;min-height:0;width:0"></span>';
	$html  .= '    <table style="font-family:Helvetica,Arial,sans-serif;border-collapse:collapse;width:100%!important;font-family:Helvetica,Arial,sans-serif;margin:0;padding:0" bgcolor="#DFDFDF" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '     <tbody>';
	$html  .= '      <tr>';
	$html  .= '       <td colspan="3">';
	$html  .= '        <table style="font-family:Helvetica,Arial,sans-serif" border="0" cellpadding="0" cellspacing="0" width="1">';
	$html  .= '         <tbody>';
	$html  .= '          <tr>';
	$html  .= '           <td>';
	$html  .= '            <div style="min-height:5px;font-size:5px;line-height:5px">';
	$html  .= '             &nbsp;';
	$html  .= '            </div></td>';
	$html  .= '          </tr>';
	$html  .= '         </tbody>';
	$html  .= '        </table></td>';
	$html  .= '      </tr>';
	$html  .= '      <tr>';
	$html  .= '       <td>';
	$html  .= '        <table style="table-layout:fixed" border="0" cellpadding="0" cellspacing="0" width="100%" align="center">';
	$html  .= '         <tbody>';
	$html  .= '          <tr>';
	$html  .= '           <td align="center">';
	$html  .= '            <table id="dori_header_nome_host" style="font-family:Helvetica,Arial,sans-serif;min-width:290px" border="0" cellpadding="0" cellspacing="0" width="900">';
	$html  .= '             <tbody>';
	$html  .= '              <tr>';
	$html  .= '               <td style="font-family:Helvetica,Arial,sans-serif">';
	
	$html  .= '                <table style="font-family:Helvetica,Arial,sans-serif" bgcolor="#333333" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '                 <tbody>';
	$html  .= '                  <tr>';
	$html  .= '                   <td width="20">';
	$html  .= '                    <table border="0" cellpadding="1" cellspacing="0" width="20">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <div style="min-height:0px;font-size:0px;line-height:0px">';
	$html  .= '                         &nbsp;';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table></td>';
	$html  .= '                   <td width="100%">';
	$html  .= '                    <table border="0" cellpadding="0" cellspacing="0" width="1">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <div style="min-height:14px;font-size:14px;line-height:14px">';
	$html  .= '                         &nbsp;';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table>';
	$html  .= '                    <table style="font-family:Helvetica,Arial,sans-serif" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td valign="middle" align="left">';
	$html  .= '                        <div style="color:#ffffff;font-size:22px;font-weight:lighter;text-align:left">';
	$html  .= '<center>' . $header_string . '</center>';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table>';
	$html  .= '                    <table border="0" cellpadding="0" cellspacing="0" width="1">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <div style="min-height:14px;font-size:14px;line-height:14px">';
	$html  .= '                         &nbsp;';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table></td>';
	$html  .= '                   <td width="20">';
	$html  .= '                    <table border="0" cellpadding="1" cellspacing="0" width="20">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <div style="min-height:0px;font-size:0px;line-height:0px">';
	$html  .= '                         &nbsp;';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table></td>';
	$html  .= '                  </tr>';
	$html  .= '                 </tbody>';
	$html  .= '                </table>';
	$html  .= '                <table style="font-family:Helvetica,Arial,sans-serif" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '                 <tbody>';
	$html  .= '                  <tr>';
	$html  .= '                   <td></td>';
	$html  .= '                  </tr>';
	$html  .= '                 </tbody>';
	$html  .= '                </table></td>';
	$html  .= '              </tr>';
	$html  .= '             </tbody>';
	$html  .= '            </table>';
	echo $html;
}


/**
 * printCategoriaHeader
 *
 * Insere o header de categoria com o nome da categoria
 *
 * @param (type) (header_string) texto exibido no header de categoria.
 */
function printCategoriaHeader($header_string){
	$html = "";	
	$html  .= '<body>';
	$html  .= '    <span style="display:none!important;font-size:1px;color:transparent;min-height:0;width:0"></span>';
	$html  .= '    <table style="font-family:Helvetica,Arial,sans-serif;border-collapse:collapse;width:100%!important;font-family:Helvetica,Arial,sans-serif;margin:0;padding:0" bgcolor="#DFDFDF" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '     <tbody>';
	$html  .= '      <tr>';
	$html  .= '       <td colspan="3">';
	$html  .= '        <table style="font-family:Helvetica,Arial,sans-serif" border="0" cellpadding="0" cellspacing="0" width="1">';
	$html  .= '         <tbody>';
	$html  .= '          <tr>';
	$html  .= '           <td>';
	$html  .= '            <div style="min-height:5px;font-size:5px;line-height:5px">';
	$html  .= '             &nbsp;';
	$html  .= '            </div></td>';
	$html  .= '          </tr>';
	$html  .= '         </tbody>';
	$html  .= '        </table></td>';
	$html  .= '      </tr>';
	$html  .= '      <tr>';
	$html  .= '       <td>';
	$html  .= '        <table style="table-layout:fixed" border="0" cellpadding="0" cellspacing="0" width="100%" align="center">';
	$html  .= '         <tbody>';
	$html  .= '          <tr>';
	$html  .= '           <td align="center">';
	$html  .= '            <table id="dori_header_nome_host" style="font-family:Helvetica,Arial,sans-serif;min-width:290px" border="0" cellpadding="0" cellspacing="0" width="900">';
	$html  .= '             <tbody>';
	$html  .= '              <tr>';
	$html  .= '               <td style="font-family:Helvetica,Arial,sans-serif">';
	
	$html  .= '                <table border="0" cellpadding="1" cellspacing="0" width="1">';
	$html  .= '                 <tbody>';
	$html  .= '                  <tr>';
	$html  .= '                   <td>';
	$html  .= '                    <div style="min-height:8px;font-size:8px;line-height:8px">';
	$html  .= '                     &nbsp;';
	$html  .= '                    </div></td>';
	$html  .= '                  </tr>';
	$html  .= '                 </tbody>';
	$html  .= '                </table>';
	
	
	$html  .= '                <table border="0" cellpadding="1" cellspacing="0" width="1">';
	$html  .= '                 <tbody>';
	$html  .= '                  <tr>';
	$html  .= '                   <td>';
	$html  .= '                    <div style="min-height:8px;font-size:8px;line-height:8px">';
	$html  .= '                     &nbsp;';
	$html  .= '                    </div></td>';
	$html  .= '                  </tr>';
	$html  .= '                 </tbody>';
	$html  .= '                </table>';
	
	
	$html  .= '                <table style="font-family:Helvetica,Arial,sans-serif" bgcolor="#333333" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '                 <tbody>';
	$html  .= '                  <tr>';
	$html  .= '                   <td width="20">';
	$html  .= '                    <table border="0" cellpadding="1" cellspacing="0" width="20">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <div style="min-height:0px;font-size:0px;line-height:0px">';
	$html  .= '                         &nbsp;';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table></td>';
	$html  .= '                   <td width="100%">';
	$html  .= '                    <table border="0" cellpadding="0" cellspacing="0" width="1">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <div style="min-height:14px;font-size:14px;line-height:14px">';
	$html  .= '                         &nbsp;';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table>';
	$html  .= '                    <table style="font-family:Helvetica,Arial,sans-serif" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td valign="middle" align="left">';
	$html  .= '                        <div style="color:#ffffff;font-size:22px;font-weight:lighter;text-align:left">';
	$html  .= '<center>' . $header_string . '</center>';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table>';
	$html  .= '                    <table border="0" cellpadding="0" cellspacing="0" width="1">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <div style="min-height:14px;font-size:14px;line-height:14px">';
	$html  .= '                         &nbsp;';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table></td>';
	$html  .= '                   <td width="20">';
	$html  .= '                    <table border="0" cellpadding="1" cellspacing="0" width="20">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <div style="min-height:0px;font-size:0px;line-height:0px">';
	$html  .= '                         &nbsp;';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table></td>';
	$html  .= '                  </tr>';
	$html  .= '                 </tbody>';
	$html  .= '                </table>';
	$html  .= '                <table style="font-family:Helvetica,Arial,sans-serif" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '                 <tbody>';
	$html  .= '                  <tr>';
	$html  .= '                   <td></td>';
	$html  .= '                  </tr>';
	$html  .= '                 </tbody>';
	$html  .= '                </table></td>';
	$html  .= '              </tr>';
	$html  .= '             </tbody>';
	$html  .= '            </table>';
	echo $html;
}


/**
 * printHeader
 *
 * Insere o header de subcategoria com o nome da subcategoria, logo acima do grafico
 *
 * @param (type) (header_string) texto exibido no header de subcategoria.
 */
function printHeader($header_string){
	$html = "";	
	$html  .= '<html id="html"><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8">';
	$html  .= '</head>';
	$html  .= '<body>';
	$html  .= '    <span style="display:none!important;font-size:1px;color:transparent;min-height:0;width:0"></span>';
	$html  .= '    <table style="font-family:Helvetica,Arial,sans-serif;border-collapse:collapse;width:100%!important;font-family:Helvetica,Arial,sans-serif;margin:0;padding:0" bgcolor="#DFDFDF" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '     <tbody>';
	$html  .= '      <tr>';
	$html  .= '       <td colspan="3">';
	$html  .= '        <table style="font-family:Helvetica,Arial,sans-serif" border="0" cellpadding="0" cellspacing="0" width="1">';
	$html  .= '         <tbody>';
	$html  .= '          <tr>';
	$html  .= '           <td>';
	$html  .= '            <div style="min-height:5px;font-size:5px;line-height:5px">';
	$html  .= '             &nbsp;';
	$html  .= '            </div></td>';
	$html  .= '          </tr>';
	$html  .= '         </tbody>';
	$html  .= '        </table></td>';
	$html  .= '      </tr>';
	$html  .= '      <tr>';
	$html  .= '       <td>';
	$html  .= '        <table style="table-layout:fixed" border="0" cellpadding="0" cellspacing="0" width="100%" align="center">';
	$html  .= '         <tbody>';
	$html  .= '          <tr>';
	$html  .= '           <td align="center">';
	$html  .= '            <table id="dori_header_nome_host" style="font-family:Helvetica,Arial,sans-serif;min-width:290px" border="0" cellpadding="0" cellspacing="0" width="900">';
	$html  .= '             <tbody>';
	$html  .= '              <tr>';
	$html  .= '               <td style="font-family:Helvetica,Arial,sans-serif">';
	$html  .= '                <table border="0" cellpadding="1" cellspacing="0" width="1">';
	$html  .= '                 <tbody>';
	$html  .= '                  <tr>';
	$html  .= '                   <td>';
	$html  .= '                    <div style="min-height:8px;font-size:8px;line-height:8px">';
	$html  .= '                     &nbsp;';
	$html  .= '                    </div></td>';
	$html  .= '                  </tr>';
	$html  .= '                 </tbody>';
	$html  .= '                </table>';
	
	$html  .= '                <table style="font-family:Helvetica,Arial,sans-serif" bgcolor="#DDDDDD" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '                 <tbody>';
	$html  .= '                  <tr>';
	$html  .= '                   <td height="21" valign="middle" width="95" align="left"><a style="text-decoration:none;border:none;display:block;min-height:21px;width:100%" href="" target="_blank"><img class="CToWUd" src="" alt="NVL IT LOGO" style="border:none;text-decoration:none" height="21" width="95"></a></td>';
	$html  .= '                   <td width="15">';
	$html  .= '                    <table border="0" cellpadding="1" cellspacing="0" width="15">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <div style="min-height:0px;font-size:0px;line-height:0px">';
	$html  .= '                         &nbsp;';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table></td>';
	//$html  .= '                   <td valign="bottom" align="left"><span style="color:#666666;font-size:16px">Relatorios</span></td>';
	$html  .= '                   <td valign="bottom" align="left"></td>';
	$html  .= '                  </tr>';
	$html  .= '                 </tbody>';
	$html  .= '                </table>'; 
	
	$html  .= '                <table border="0" cellpadding="1" cellspacing="0" width="1">';
	$html  .= '                 <tbody>';
	$html  .= '                  <tr>';
	$html  .= '                   <td>';
	$html  .= '                    <div style="min-height:8px;font-size:8px;line-height:8px">';
	$html  .= '                     &nbsp;';
	$html  .= '                    </div></td>';
	$html  .= '                  </tr>';
	$html  .= '                 </tbody>';
	$html  .= '                </table>';
	$html  .= '                <table style="font-family:Helvetica,Arial,sans-serif" bgcolor="#333333" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '                 <tbody>';
	$html  .= '                  <tr>';
	$html  .= '                   <td width="20">';
	$html  .= '                    <table border="0" cellpadding="1" cellspacing="0" width="20">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <div style="min-height:0px;font-size:0px;line-height:0px">';
	$html  .= '                         &nbsp;';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table></td>';
	$html  .= '                   <td width="100%">';
	$html  .= '                    <table border="0" cellpadding="0" cellspacing="0" width="1">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <div style="min-height:14px;font-size:14px;line-height:14px">';
	$html  .= '                         &nbsp;';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table>';
	$html  .= '                    <table style="font-family:Helvetica,Arial,sans-serif" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td valign="middle" align="left">';
	$html  .= '                        <div style="color:#ffffff;font-size:22px;font-weight:lighter;text-align:left">';
	$html  .= '<center>' . $header_string . '</center>';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table>';
	$html  .= '                    <table border="0" cellpadding="0" cellspacing="0" width="1">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <div style="min-height:14px;font-size:14px;line-height:14px">';
	$html  .= '                         &nbsp;';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table></td>';
	$html  .= '                   <td width="20">';
	$html  .= '                    <table border="0" cellpadding="1" cellspacing="0" width="20">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <div style="min-height:0px;font-size:0px;line-height:0px">';
	$html  .= '                         &nbsp;';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table></td>';
	$html  .= '                  </tr>';
	$html  .= '                 </tbody>';
	$html  .= '                </table>';
	$html  .= '                <table style="font-family:Helvetica,Arial,sans-serif" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '                 <tbody>';
	$html  .= '                  <tr>';
	$html  .= '                   <td></td>';
	$html  .= '                  </tr>';
	$html  .= '                 </tbody>';
	$html  .= '                </table></td>';
	$html  .= '              </tr>';
	$html  .= '             </tbody>';
	$html  .= '            </table>';
	echo $html;
}
?>