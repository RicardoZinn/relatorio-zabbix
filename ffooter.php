<?php 
/**
 * getFooterHTML
 *
 * Insere o rodape da pagina com o nome da empresa e o nome do responsavel.
 *
 * @param (type) (empresa_nome) nome da empresa.
 * @param (type) (responsavel_nome) nome do responsavel principal na empresa.
 */
function getFooterHTML($empresa_nome,$responsavel_nome){
	$html   = '';
	$html  .= '			<!--CARD-final-->';
	$html  .= '			<table id="card_final" style="font-family:Helvetica,Arial,sans-serif" border="0" cellpadding="0" cellspacing="0" width="900">';
	$html  .= '             <tbody>';
	$html  .= '              <tr>';
	$html  .= '               <td align="left">';
	$html  .= '                <table style="font-family:Helvetica,Arial,sans-serif" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '                 <tbody>';
	$html  .= '                  <tr>';
	$html  .= '                   <td>';
	$html  .= '                    <table border="0" cellpadding="1" cellspacing="0" width="1">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <div style="min-height:10px;font-size:10px;line-height:10px">';
	$html  .= '                         &nbsp;';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table></td>';
	$html  .= '                  </tr>';
	$html  .= '                  <tr>';
	$html  .= '                   <td align="left">';
	$html  .= '                    <table style="font-family:Helvetica,Arial,sans-serif;font-size:11px;font-family:Helvetica,Arial,sans-serif;color:#999999" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <table border="0" cellpadding="0" cellspacing="0" width="1">';
	$html  .= '                         <tbody>';
	$html  .= '                          <tr>';
	$html  .= '                           <td>';
	$html  .= '                            <div style="min-height:10px;font-size:10px;line-height:10px">';
	$html  .= '                             &nbsp;';
	$html  .= '                            </div></td>';
	$html  .= '                          </tr>';
	$html  .= '                         </tbody>';
	$html  .= '                        </table></td>';
	$html  .= '                      </tr>';
	$html  .= '                      <tr>';
	$html  .= '                       <td align="center">Você está recebendo e-mails resumindo as atividades monitoradas pelo ZABBIX. <a style="text-decoration:none;color:#0077b5" href="link_cancelar" target="_blank">Cancelar inscrição</a></td>';
	$html  .= '                      </tr>';
	$html  .= '                      <tr>';
	$html  .= '                       <td align="center">Este e-mail foi enviado para ' . $responsavel_nome . ' (' . $empresa_nome . '). <a style="text-decoration:none;color:#0077b5" href="link_saiba_pq" target="_blank">Saiba por que incluí­mos isto.</a></td>';
	$html  .= '                      </tr>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <table border="0" cellpadding="1" cellspacing="0" width="1">';
	$html  .= '                         <tbody>';
	$html  .= '                          <tr>';
	$html  .= '                           <td>';
	$html  .= '                            <div style="min-height:10px;font-size:10px;line-height:10px">';
	$html  .= '                             &nbsp;';
	$html  .= '                            </div></td>';
	$html  .= '                          </tr>';
	$html  .= '                         </tbody>';
	$html  .= '                        </table></td>';
	$html  .= '                      </tr>';
	$html  .= '                      <tr>';
	$html  .= '                       <td dir="ltr" align="center">© 2015 NVL IT é um nome comercial registrado da Fábio Hoclatiner Gomes ME.</td>';
	$html  .= '                      </tr>';
	$html  .= '                      <tr>';
	$html  .= '                       <td dir="ltr" align="center">Registrada no Brasil como uma empresa Privada e Ltda. Registro número 13.317.092/0001-80.</td>';
	$html  .= '                      </tr>';
	$html  .= '                      <tr>';
	$html  .= '                       <td dir="ltr" align="center">Estamos na Frei Estanislau Schaette, 1326, Sala 02, Agua Verde, Blumenau, SC, CEP 89037-002</td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table></td>';
	$html  .= '                  </tr>';
	$html  .= '                  <tr>';
	$html  .= '                   <td>';
	$html  .= '                    <table border="0" cellpadding="1" cellspacing="0" width="1">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <div style="min-height:20px;font-size:20px;line-height:20px">';
	$html  .= '                         &nbsp;';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table></td>';
	$html  .= '                  </tr>';
	$html  .= '                 </tbody>';
	$html  .= '                </table></td>';
	$html  .= '              </tr>';
	$html  .= '             </tbody>';
	$html  .= '            </table>';
	$html  .= '            <!--começa o fim do html-->';
	$html  .= '           </td>';
	$html  .= '          </tr>';
	$html  .= '         </tbody>';
	$html  .= '        </table></td>';
	$html  .= '      </tr>';
	$html  .= '     </tbody>';
	$html  .= '    </table>';
	$html  .= '    <img class="CToWUd" src="" style="width:1px;min-height:1px"><div class="yj6qo"></div><div class="adL">';
	$html  .= '   </div>';
	$html  .= '</body></html>';
	return $html;
}
?>