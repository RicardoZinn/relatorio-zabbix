<?php 
/**
 * printBody
 *
 * Insere html com a imagem que retorna da API do HighChart, insere tambem quando tiver a quantidade de eventos nas chaves contidas no grafico.
 *
 * @param (type) (identificador) identificador da imagem.
 * @param (type) (string_json) string com os dados para plotar o grafico.
 * @param (type) (qtd_eventos) quantidade de eventos no ultimo mes para as chaves do grafico.
 */
function printBody($identificador,$string_json,$qtd_eventos){
	
	$url = 'http://export.highcharts.com/?content=options&options=' . $string_json . '&type=image/png&width=800&scale=&constr=Chart';
	$url_encoded = 'http://export.highcharts.com/?content=options&options='. utf8_decode(urlencode($string_json)) .'&type=image/png&width=800&scale=&constr=Chart';
		
	$html  .= '			<!--CARD-xx-->';
	$html  .= '			<table id="dori_card_com_grafico" class="card" style="font-family:Helvetica,Arial,sans-serif;width:900px;table-layout:fixed;text-align:left;background:#ffffff;border:1px solid #ffffff;border-bottom:2px solid #bcbcbc;border-left:1px solid #cecece;border-right:1px solid #cecece" border="0" cellpadding="0" cellspacing="0" width="900">';
	$html  .= '             <tbody>';
	$html  .= '              <tr>';
	$html  .= '               <td>';
	$html  .= '                <table style="font-family:Helvetica,Arial,sans-serif" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '                 <tbody>';
	$html  .= '                  <tr>';
	$html  .= '                   <td width="20">';
	$html  .= '                    <table border="0" cellpadding="1" cellspacing="0" width="1">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <div style="min-height:0px;font-size:0px;line-height:0px">';
	$html  .= '                         &nbsp;';
	$html  .= '                        </div></td>';
	$html  .= '                      </tr>';
	$html  .= '                     </tbody>';
	$html  .= '                    </table></td>';
	$html  .= '                   <td>';
	$html  .= '                    <table style="font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:15px;word-wrap:break-word" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '                     <tbody>';
	$html  .= '                      <tr>';
	$html  .= '                       <td>';
	$html  .= '                        <table border="0" cellpadding="1" cellspacing="0" width="1">';
	$html  .= '                         <tbody>';
	$html  .= '                          <tr>';
	$html  .= '                           <td>';
	$html  .= '                            <div style="min-height:20px;font-size:20px;line-height:20px">';
	$html  .= '                             &nbsp;';
	$html  .= '                            </div></td>';
	$html  .= '                          </tr>';
	$html  .= '                         </tbody>';
	$html  .= '                        </table></td>';
	$html  .= '                      </tr>';
	$html  .= '                      ';
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
	$html  .= '                       <td style="font-size:18px;line-height:22px">';
	$html  .= '                          <div class="container_imagem" style="vertical-align:middle; text-align:center">';
	$html  .= '                            <img id="grafico_png_'.$identificador.'" alt="' . $url_encoded . '" src="' . $url_encoded . '"></img>';
	$html  .= '                          </div>';
	$html  .= '						  ';
	$html  .= '					   </td>';
	$html  .= '                      </tr>';
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
	$html  .= '                       <td>';
	$html  .= '                        <table style="font-family:Helvetica,Arial,sans-serif" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '                         <tbody>';
	$html  .= '                          <tr>';
	$html  .= '                           <td>';
	$html  .= '                            <table style="font-family:Helvetica,Arial,sans-serif" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '                             <tbody>';
	$html  .= '                              ';
	$html  .= '                              <tr>';
	$html  .= '                               <td>';
	$html  .= '                                <table border="0" cellpadding="1" cellspacing="0" width="1">';
	$html  .= '                                 <tbody>';
	$html  .= '                                  <tr>';
	$html  .= '                                   <td>';
	$html  .= '                                    <div style="min-height:10px;font-size:10px;line-height:10px">';
	$html  .= '                                     &nbsp;';
	$html  .= '                                    </div></td>';
	$html  .= '                                  </tr>';
	$html  .= '                                 </tbody>';
	$html  .= '                                </table></td>';
	$html  .= '                              </tr>';
	
	/***
	** Trata se houve algum evento no periodo, imprime uma imagem com o nome do analista e um comentario simples com a quantidade de eventos no periodo
	***/ 	
	if($qtd_eventos>0){
		$msg_padrao_evento = "1 evento";
		if($qtd_eventos>1)
		$msg_padrao_evento = $qtd_eventos ." eventos";
		
		$html  .= '                              <!-- dori nessa linha contem a imagem do analista e o comentario do grafico -->';
		$html  .= '								<tr>';
		$html  .= '                               <td>';
		$html  .= '                                <table style="font-family:Helvetica,Arial,sans-serif;background:#eeeeee" border="0" cellpadding="0" cellspacing="0" width="100%">';
		$html  .= '                                 <tbody>';
		$html  .= '                                  <tr>';
		$html  .= '                                   <td>';
		$html  .= '                                    <table border="0" cellpadding="1" cellspacing="0" width="1">';
		$html  .= '                                     <tbody>';
		$html  .= '                                      <tr>';
		$html  .= '                                       <td>';
		$html  .= '                                        <div style="min-height:5px;font-size:5px;line-height:5px">';
		$html  .= '                                         &nbsp;';
		$html  .= '                                        </div></td>';
		$html  .= '                                      </tr>';
		$html  .= '                                     </tbody>';
		$html  .= '                                    </table></td>';
		$html  .= '                                  </tr>';
		$html  .= '                                  <tr>';
		$html  .= '                                   <td>';
		$html  .= '                                    <table style="font-family:Helvetica,Arial,sans-serif" border="0" cellpadding="0" cellspacing="0" width="100%">';
		$html  .= '                                     <tbody>';
		$html  .= '                                      <tr>';
		$html  .= '                                       <td width="30"><a href="" style="text-decoration:none" target="_blank"><img class="CToWUd" src="https://media.licdn.com/mpr/mpr/shrink_100_100/AAEAAQAAAAAAAAKLAAAAJGJiNDU2NmNlLWMxNTYtNDhjNy05NDBhLWFmZDE2ZDM5NDEzYg.jpg" alt="Dori" style="border:none;outline:none;text-decoration:none;display:block" height="30" width="30"></a></td>';
		$html  .= '                                       <td width="9">';
		$html  .= '                                        <table border="0" cellpadding="1" cellspacing="0" width="9">';
		$html  .= '                                         <tbody>';
		$html  .= '                                          <tr>';
		$html  .= '                                           <td>';
		$html  .= '                                            <div style="min-height:0px;font-size:0px;line-height:0px">';
		$html  .= '                                             &nbsp;';
		$html  .= '                                            </div></td>';
		$html  .= '                                          </tr>';
		$html  .= '                                         </tbody>';
		$html  .= '                                        </table></td>';
		$html  .= '                                       <td>';
		$html  .= '                                        <table style="font-family:Helvetica,Arial,sans-serif" border="0" cellpadding="0" cellspacing="0" width="100%">';
		$html  .= '                                         <tbody>';
		$html  .= '                                          <tr>';
		$html  .= '                                           <td>';
		$html  .= '                                            <table style="font-family:Helvetica,Arial,sans-serif;font-size:11px" border="0" cellpadding="0" cellspacing="0" width="100%">';
		$html  .= '                                             <tbody>';
		$html  .= '                                              <tr>';
		$html  .= '                                               <td><a href="" style="color:#333333;text-decoration:none;font-size:11px" target="_blank">Dori</a></td>';
		$html  .= '                                              </tr>';
		$html  .= '                                             </tbody>';
		$html  .= '                                            </table></td>';
		$html  .= '                                          </tr>';
		$html  .= '                                          <tr>';
		$html  .= '                                           <td>';
		$html  .= '                                            <table border="0" cellpadding="1" cellspacing="0" width="1">';
		$html  .= '                                             <tbody>';
		$html  .= '                                              <tr>';
		$html  .= '                                               <td>';
		$html  .= '                                                <div style="min-height:3px;font-size:3px;line-height:3px">';
		$html  .= '                                                 &nbsp;';
		$html  .= '                                                </div></td>';
		$html  .= '                                              </tr>';
		$html  .= '                                             </tbody>';
		$html  .= '                                            </table></td>';
		$html  .= '                                          </tr>';
		$html  .= '                                          <tr>';
		$html  .= '                                           <td class="comentario" style="text-decoration:none;font-size:13px;color:#333333">Identificamos ' . $msg_padrao_evento . ' no monitoramento, favor abrir um chamado.</td>';
		$html  .= '                                          </tr>';
		$html  .= '                                         </tbody>';
		$html  .= '                                        </table></td>';
		$html  .= '                                      </tr>';
		$html  .= '                                     </tbody>';
		$html  .= '                                    </table></td>';
		$html  .= '                                  </tr>';
		$html  .= '                                  <tr>';
		$html  .= '                                   <td style="border-bottom:2px solid #fff">';
		$html  .= '                                    <table border="0" cellpadding="1" cellspacing="0" width="1">';
		$html  .= '                                     <tbody>';
		$html  .= '                                      <tr>';
		$html  .= '                                       <td>';
		$html  .= '                                        <div style="min-height:5px;font-size:5px;line-height:5px">';
		$html  .= '                                         &nbsp;';
		$html  .= '                                        </div></td>';
		$html  .= '                                      </tr>';
		$html  .= '                                     </tbody>';
		$html  .= '                                    </table></td>';
		$html  .= '                                  </tr>';
		$html  .= '                                 </tbody>';
		$html  .= '                                </table>';
		$html  .= '                                <table style="font-family:Helvetica,Arial,sans-serif" border="0" cellpadding="0" cellspacing="0" width="100%">';
		$html  .= '                                 <tbody>';
		$html  .= '                                  <tr>';
		$html  .= '                                   <td><a href="" style="color:#2d8cd7;text-decoration:none;font-size:0;max-height:0;min-height:0" target="_blank"><span>Adicionar comentário </span><img class="CToWUd" src="https://ci6.googleusercontent.com/proxy/HCUMHnecoxt3Zo71p9peVaGBl8qNKK5_pReAVHn6DSxmdh0bI2JqByMGXf5cK0OSAXwmLQNGU9LCFqZ4P0fRDL2lcA7VHtSl3bWG-lOBSByCKKl2NasgWCn8Yw=s0-d-e1-ft#https://static.licdn.com/scds/common/u/img/email/arrow_right_blue.png" style="border:none;outline:none;text-decoration:none;max-height:0;min-height:0;font-size:0" alt="" height="8" width="4"></a></td>';
		$html  .= '                                  </tr>';
		$html  .= '                                 </tbody>';
		$html  .= '                                </table></td>';
		$html  .= '                              </tr>';
	}
	
	
	$html  .= '                              <tr>';
	$html  .= '                               <td>';
	$html  .= '                                <table border="0" cellpadding="1" cellspacing="0" width="1">';
	$html  .= '                                 <tbody>';
	$html  .= '                                  <tr>';
	$html  .= '                                   <td>';
	$html  .= '                                    <div style="min-height:30px;font-size:30px;line-height:30px">';
	$html  .= '                                     &nbsp;';
	$html  .= '                                    </div></td>';
	$html  .= '                                  </tr>';
	$html  .= '                                 </tbody>';
	$html  .= '                                </table></td>';
	$html  .= '                              </tr>';
	$html  .= '                             </tbody>';
	$html  .= '                            </table></td>';
	$html  .= '                          </tr>';
	$html  .= '                         </tbody>';
	$html  .= '                        </table></td>';
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
	$html  .= '                </table></td>';
	$html  .= '              </tr>';
	$html  .= '             </tbody>';
	$html  .= '            </table>';	

	/**
	** Cria um espacamento entre os "cards"
	**/
	$html  .= '			<!--ESPACAMENTO-xx-->';
	$html  .= '            <table id="espacamento_entre_card" style="font-family:Helvetica,Arial,sans-serif" border="0" cellpadding="0" cellspacing="0" width="100%">';
	$html  .= '             <tbody>';
	$html  .= '              <tr>';
	$html  .= '               <td height="10">';
	$html  .= '                <table border="0" cellpadding="1" cellspacing="0" width="1">';
	$html  .= '                 <tbody>';
	$html  .= '                  <tr>';
	$html  .= '                   <td>';
	$html  .= '                    <div style="min-height:0px;font-size:0px;line-height:0px">';
	$html  .= '                     &nbsp;';
	$html  .= '                    </div></td>';
	$html  .= '                  </tr>';
	$html  .= '                 </tbody>';
	$html  .= '                </table></td>';
	$html  .= '              </tr>';
	$html  .= '             </tbody>';
	$html  .= '            </table>';		
		
	echo $html;
}
?>