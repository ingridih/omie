
<?php 
include '../../pdo/pdo.php';


faturar_tudo();

function criar_nf_entrada(){
        
    $Conexao = ConexaoMYSQL_prod::getConnection();
    $ConexaoREPORTS = ConexaoMYSQL_reports::getConnection();

    $arrayCredenciaisK = array(
        'CRECENCIALKEY' => '11111111111',
    );
    $arrayCredenciaisS = array(
        'CREDENCIALSECRET' => '111111111111111111111111111111111111111'
    );
    $arrayCFOP = array(
        '6.108' => '2.202',
        '5.405' => '1.411',
        '5.102' => '1.102',
        '6.910' => '2.910'
    );

    $pisCofins = array(
        '01' => '50',
        '04' => '70',
        '06' => '70'
    );

    $urlcliente = 'https://app.omie.com.br/api/v1/geral/clientes/';

    $url = 'https://app.omie.com.br/api/v1/produtos/pedido/';
    $headers = array(
        'Content-type: application/json',
    );

    //CONSULTA R1 - PEDIDOS COM REENVIO 1. - sem difereça de produtos. 
    // $query2 = $ConexaoREPORTS->query("SELECT * FROM registro_reenvio 
    // where codigo_pedido_original is not null and (codigo_pedido is not null and codigo_pedido <> '1234' and codigo_pedido_original <> '0' and length(codigo_pedido) < 15) and  numero_pedido like '%R1' and nf_entrada is null ");
    // $rows = $query2->fetchAll();
    // foreach ($rows as $row) {

    //     $empresaAtual = $row['empresa'];
    //     $pedido_original = $row['numero_pedido_original'];
    //     $novo_pedido = $row['numero_pedido'];
    //     $codigo_original = $row['codigo_pedido_original'];
    //     $novo_codigo = $row['codigo_pedido'];

    //      // ---------------------- procurar NF atual se está cancelado ou nao --------------------------
    //      $data_sts = array(
    //         'call' => 'StatusPedido',
    //         'app_key' => $arrayCredenciaisK[$empresaAtual],
    //         'app_secret' => $arrayCredenciaisS[$empresaAtual],
    //         'param' => array(
    //             array('codigo_pedido' => $novo_codigo),
    //         ),
    //     );
    //     $optionsnf = array(
    //         CURLOPT_URL => $url,
    //         CURLOPT_HTTPHEADER => $headers,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_POST => true,
    //         CURLOPT_POSTFIELDS => json_encode($data_sts),
    //     );
    
    //     $curlch = curl_init();
    //     curl_setopt_array($curlch, $optionsnf);
    
    //     $chavenf = curl_exec($curlch);
    //     $respostachavenf0 = json_decode($chavenf, true);

    //    // preciso da chave nf para montar a nota de entrada.
    //     if(isset($respostachavenf0['ListaNfe'][0]['chave_nfe']) and $respostachavenf0['cancelada'] != 'S'){

    //         $data_sts = array(
    //             'call' => 'StatusPedido',
    //             'app_key' => $arrayCredenciaisK[$empresaAtual],
    //             'app_secret' => $arrayCredenciaisS[$empresaAtual],
    //             'param' => array(
    //                 array('codigo_pedido' => $codigo_original), // pega a NF anterior. A original no caso, sem R1
    //             ),
    //         );
    //         $optionsnf = array(
    //             CURLOPT_URL => $url,
    //             CURLOPT_HTTPHEADER => $headers,
    //             CURLOPT_RETURNTRANSFER => true,
    //             CURLOPT_POST => true,
    //             CURLOPT_POSTFIELDS => json_encode($data_sts),
    //         );
        
    //         $curlch = curl_init();
    //         curl_setopt_array($curlch, $optionsnf);
        
    //         $chavenf = curl_exec($curlch);
    //         $respostachavenf = json_decode($chavenf, true);

    //         // preciso da chave nf para montar a nota de entrada.
    //         if(isset($respostachavenf['ListaNfe'][0]['chave_nfe']) and $respostachavenf['cancelada'] != 'S'){ 

    //             // -------------------------- CONSULTAR PARA MONTAR A NF DE ENTRADA - pedido atual - novo -----------------------------
    //             $data = array(
    //                 'call' => 'ConsultarPedido',
    //                 'app_key' => $arrayCredenciaisK[$empresaAtual],
    //                 'app_secret' => $arrayCredenciaisS[$empresaAtual],
    //                 'param' => array(
    //                     array('codigo_pedido_integracao' => $pedido_original),
    //                 ),
    //             );

    //             $options = array(
    //                 CURLOPT_URL => $url,
    //                 CURLOPT_HTTPHEADER => $headers,
    //                 CURLOPT_RETURNTRANSFER => true,
    //                 CURLOPT_POST => true,
    //                 CURLOPT_POSTFIELDS => json_encode($data),
    //             );

    //             $curl = curl_init();
    //             curl_setopt_array($curl, $options);

    //             $response = curl_exec($curl);
    //             $resposta = json_decode($response, true);

    //             if(isset($resposta['pedido_venda_produto'])){
    //                 $cCodIntNotaEnt = $resposta['pedido_venda_produto']['cabecalho']['codigo_pedido'];
    //                 $dPrevisao = $resposta['pedido_venda_produto']['cabecalho']['data_previsao'];
    //                 $nCodCli = $resposta['pedido_venda_produto']['cabecalho']['codigo_cliente'];

    //                 $data_cliente = array(
    //                     'call' => 'ConsultarCliente',
    //                     'app_key' => $arrayCredenciaisK[$empresaAtual],
    //                     'app_secret' => $arrayCredenciaisS[$empresaAtual],
    //                     'param' => array(
    //                         array('codigo_cliente_omie' => $nCodCli),
    //                     ),
    //                 );
    //                 $option_cliente = array(
    //                     CURLOPT_URL => $urlcliente,
    //                     CURLOPT_HTTPHEADER => $headers,
    //                     CURLOPT_RETURNTRANSFER => true,
    //                     CURLOPT_POST => true,
    //                     CURLOPT_POSTFIELDS => json_encode($data_cliente),
    //                 );
                
    //                 $curlch = curl_init();
    //                 curl_setopt_array($curlch, $option_cliente);
                
    //                 $clienteUF = curl_exec($curlch);
    //                 $EstadoCli = json_decode($clienteUF, true);



    //                 $script = '{
    //                     "cabec": {
    //                     "cCodIntNotaEnt": "'.$cCodIntNotaEnt.'",
    //                     "dPrevisao": "'.date('d/m/Y').'",
    //                     "nCodCli": "'.$nCodCli.'"
    //                     },
    //                     "infAdic": {
    //                     "cCodCateg": "2.09.01",
    //                     "cPedido": "'.$pedido_original.'",
    //                     "nfRelacionada":[{
    //                         "cChaveRef": "'.$respostachavenf['ListaNfe'][0]['chave_nfe'].'"
    //                     }]
    //                     },
                    
    //                     "produtos": [';

    //                 $arrayJuntosIguais = array();
                    
    //                 // aqui vou montar o array buscando os itens da nota original para comparar com o novo, os itens que forem iguais, vou salvar e montar no array. 
    //                 if($row['motivo'] == 'produto' or $row['motivo'] == 'endereco_e_produto'){
    //                     // vou consultar os itens da nota atual. 
    //                     $datanovo = array(
    //                         'call' => 'ConsultarPedido',
    //                         'app_key' => $arrayCredenciaisK[$empresaAtual],
    //                         'app_secret' => $arrayCredenciaisS[$empresaAtual],
    //                         'param' => array(
    //                             array('codigo_pedido_integracao' => $novo_pedido),
    //                         ),
    //                     );
        
    //                     $options_novo = array(
    //                         CURLOPT_URL => $url,
    //                         CURLOPT_HTTPHEADER => $headers,
    //                         CURLOPT_RETURNTRANSFER => true,
    //                         CURLOPT_POST => true,
    //                         CURLOPT_POSTFIELDS => json_encode($datanovo),
    //                     );
        
    //                     $curl_novo = curl_init();
    //                     curl_setopt_array($curl_novo, $options_novo);
        
    //                     $response_novo = curl_exec($curl_novo);
    //                     $resposta_novo = json_decode($response_novo, true);

    //                     if(isset($resposta_novo['pedido_venda_produto']['det'])){ 

    //                         foreach($resposta_novo['pedido_venda_produto']['det'] as $detnv){

    //                             foreach($resposta['pedido_venda_produto']['det'] as $det){
                                   
    //                                 if(isset($arrayCFOP[$detnv['produto']['cfop']]) and $arrayCFOP[$detnv['produto']['cfop']] != '' and $detnv['produto']['codigo'] == $det['produto']['codigo']){
                                   
    //                                     $cCFOP = $arrayCFOP[$detnv['produto']['cfop']];
            
    //                                     $cCodItInt = $detnv['produto']['codigo'];
    //                                     $nCodProd = $detnv['produto']['codigo_produto'];
    //                                     $codigo_local_estoque = $detnv['inf_adic']['codigo_local_estoque'];
    //                                     $nQtd = $detnv['produto']['quantidade'];
    //                                     $nValUnit = $detnv['produto']['valor_total'] / $detnv['produto']['quantidade'];
            
    //                                     $cSitTribPIS = $det['imposto']['pis_padrao']['cod_sit_trib_pis'];
    //                                     $cSitTribCOFINS = $det['imposto']['cofins_padrao']['cod_sit_trib_cofins'];
    //                                     $cSitTrib = $det['imposto']['icms']['cod_sit_trib_icms'];
    //                                     $cOrigem = $det['imposto']['icms']['origem_icms'];
    //                                     $nAliq = $det['imposto']['icms']['aliq_icms'];
    //                                     $nAliqCOFINS = $det['imposto']['cofins_padrao']['aliq_cofins'];
    //                                     $nAliqPIS = $det['imposto']['pis_padrao']['aliq_pis'];
            
    //                                     if (isset($arrayJuntosIguais[$cCodItInt])) {
    //                                         $arrayJuntosIguais[$cCodItInt]['nValUnit'] += $nValUnit * $detnv['produto']['quantidade'];
    //                                         $arrayJuntosIguais[$cCodItInt]['nQtde'] += $detnv['produto']['quantidade'];
    //                                     } else {
    //                                         // Se não, inicialize os valores
    //                                         $arrayJuntosIguais[$cCodItInt] = array(
    //                                             'nValUnit' => $nValUnit * $detnv['produto']['quantidade'],
    //                                             'nQtde' => $detnv['produto']['quantidade'],
    //                                             'cCFOP' => $arrayCFOP[$detnv['produto']['cfop']],
    //                                             'nCodProd' => $detnv['produto']['codigo_produto'],
    //                                             'codigo_local_estoque' => $detnv['inf_adic']['codigo_local_estoque'],
    //                                             'PIS' => array(
    //                                                 'cSitTribPIS' => $pisCofins[$detnv['imposto']['pis_padrao']['cod_sit_trib_pis']],
    //                                                 'nAliqPIS' => $detnv['imposto']['pis_padrao']['aliq_pis']
    //                                             ),
    //                                             'COFINS' => array(
    //                                                 'cSitTribCOFINS' => $pisCofins[$detnv['imposto']['cofins_padrao']['cod_sit_trib_cofins']],
    //                                                 'nAliqCOFINS' => $detnv['imposto']['cofins_padrao']['aliq_cofins']
    //                                             ),
    //                                             'ICMS' => array(
    //                                                 'cSitTrib' => $detnv['imposto']['icms']['cod_sit_trib_icms'],
    //                                                 'cOrigem' => $detnv['imposto']['icms']['origem_icms'],
    //                                                 'nAliq' => $detnv['imposto']['icms']['aliq_icms']
    //                                             )
    //                                         );
    //                                     }
    //                                 }else if(isset($arrayCFOP[$detnv['produto']['cfop']]) and $arrayCFOP[$detnv['produto']['cfop']] != '' and strstr($det['produto']['descricao'], 'Assinatura')){
    //                                     $cCFOP = $arrayCFOP[$detnv['produto']['cfop']];
            
    //                                     $cCodItInt = $det['produto']['codigo'];
    //                                     $nCodProd = $det['produto']['codigo_produto'];
    //                                     $codigo_local_estoque = $detnv['inf_adic']['codigo_local_estoque'];
    //                                     $nQtd = $detnv['produto']['quantidade'];
    //                                     $nValUnit = $detnv['produto']['valor_total'] / $detnv['produto']['quantidade'];
            
    //                                     $cSitTribPIS = $det['imposto']['pis_padrao']['cod_sit_trib_pis'];
    //                                     $cSitTribCOFINS = $det['imposto']['cofins_padrao']['cod_sit_trib_cofins'];
    //                                     $cSitTrib = $det['imposto']['icms']['cod_sit_trib_icms'];
    //                                     $cOrigem = $det['imposto']['icms']['origem_icms'];
    //                                     $nAliq = $det['imposto']['icms']['aliq_icms'];
    //                                     $nAliqCOFINS = $det['imposto']['cofins_padrao']['aliq_cofins'];
    //                                     $nAliqPIS = $det['imposto']['pis_padrao']['aliq_pis'];
            
    //                                     if (isset($arrayJuntosIguais[$cCodItInt])) {
    //                                         $arrayJuntosIguais[$cCodItInt]['nValUnit'] += $nValUnit * $detnv['produto']['quantidade'];
    //                                         $arrayJuntosIguais[$cCodItInt]['nQtde'] += $detnv['produto']['quantidade'];
    //                                     } else {
    //                                         // Se não, inicialize os valores
    //                                         $arrayJuntosIguais[$cCodItInt] = array(
    //                                             'nValUnit' => $nValUnit * $detnv['produto']['quantidade'],
    //                                             'nQtde' => $detnv['produto']['quantidade'],
    //                                             'cCFOP' => $arrayCFOP[$detnv['produto']['cfop']],
    //                                             'nCodProd' => $detnv['produto']['codigo_produto'],
    //                                             'codigo_local_estoque' => $detnv['inf_adic']['codigo_local_estoque'],
    //                                             'PIS' => array(
    //                                                 'cSitTribPIS' => $pisCofins[$detnv['imposto']['pis_padrao']['cod_sit_trib_pis']],
    //                                                 'nAliqPIS' => $detnv['imposto']['pis_padrao']['aliq_pis']
    //                                             ),
    //                                             'COFINS' => array(
    //                                                 'cSitTribCOFINS' => $pisCofins[$detnv['imposto']['cofins_padrao']['cod_sit_trib_cofins']],
    //                                                 'nAliqCOFINS' => $detnv['imposto']['cofins_padrao']['aliq_cofins']
    //                                             ),
    //                                             'ICMS' => array(
    //                                                 'cSitTrib' => $detnv['imposto']['icms']['cod_sit_trib_icms'],
    //                                                 'cOrigem' => $detnv['imposto']['icms']['origem_icms'],
    //                                                 'nAliq' => $detnv['imposto']['icms']['aliq_icms']
    //                                             )
    //                                         );
    //                                     }
    //                                 }else if(isset($arrayCFOP[$detnv['produto']['cfop']]) and $arrayCFOP[$detnv['produto']['cfop']] != '' and $detnv['produto']['codigo'] != $det['produto']['codigo']){
    //                                     $cCFOP = $arrayCFOP[$detnv['produto']['cfop']];
            
    //                                     $cCodItInt = $det['produto']['codigo'];
    //                                     $nCodProd = $det['produto']['codigo_produto'];
    //                                     $codigo_local_estoque = $detnv['inf_adic']['codigo_local_estoque'];
    //                                     $nQtd = $detnv['produto']['quantidade'];
    //                                     $nValUnit = $detnv['produto']['valor_total'] / $detnv['produto']['quantidade'];
            
    //                                     $cSitTribPIS = $det['imposto']['pis_padrao']['cod_sit_trib_pis'];
    //                                     $cSitTribCOFINS = $det['imposto']['cofins_padrao']['cod_sit_trib_cofins'];
    //                                     $cSitTrib = $det['imposto']['icms']['cod_sit_trib_icms'];
    //                                     $cOrigem = $det['imposto']['icms']['origem_icms'];
    //                                     $nAliq = $det['imposto']['icms']['aliq_icms'];
    //                                     $nAliqCOFINS = $det['imposto']['cofins_padrao']['aliq_cofins'];
    //                                     $nAliqPIS = $det['imposto']['pis_padrao']['aliq_pis'];
            
    //                                     if (isset($arrayJuntosIguais[$cCodItInt])) {
    //                                         $arrayJuntosIguais[$cCodItInt]['nValUnit'] += $nValUnit * $detnv['produto']['quantidade'];
    //                                         $arrayJuntosIguais[$cCodItInt]['nQtde'] += $detnv['produto']['quantidade'];
    //                                     } else {
    //                                         // Se não, inicialize os valores
    //                                         $arrayJuntosIguais[$cCodItInt] = array(
    //                                             'nValUnit' => $nValUnit * $detnv['produto']['quantidade'],
    //                                             'nQtde' => $detnv['produto']['quantidade'],
    //                                             'cCFOP' => $arrayCFOP[$detnv['produto']['cfop']],
    //                                             'nCodProd' => $detnv['produto']['codigo_produto'],
    //                                             'codigo_local_estoque' => $detnv['inf_adic']['codigo_local_estoque'],
    //                                             'PIS' => array(
    //                                                 'cSitTribPIS' => $pisCofins[$detnv['imposto']['pis_padrao']['cod_sit_trib_pis']],
    //                                                 'nAliqPIS' => $detnv['imposto']['pis_padrao']['aliq_pis']
    //                                             ),
    //                                             'COFINS' => array(
    //                                                 'cSitTribCOFINS' => $pisCofins[$detnv['imposto']['cofins_padrao']['cod_sit_trib_cofins']],
    //                                                 'nAliqCOFINS' => $detnv['imposto']['cofins_padrao']['aliq_cofins']
    //                                             ),
    //                                             'ICMS' => array(
    //                                                 'cSitTrib' => $detnv['imposto']['icms']['cod_sit_trib_icms'],
    //                                                 'cOrigem' => $detnv['imposto']['icms']['origem_icms'],
    //                                                 'nAliq' => $detnv['imposto']['icms']['aliq_icms']
    //                                             )
    //                                         );
    //                                     }
    //                                 }
    //                             }
                                
    //                         }
    //                     }
    //                 }else{  // aqui são os pedidos que possuem todos os itens iguais, nao farei a tratativa. 
    //                     foreach($resposta['pedido_venda_produto']['det'] as $det){
    //                         if(isset($arrayCFOP[$det['produto']['cfop']]) and $arrayCFOP[$det['produto']['cfop']] != ''){
    //                             $cCFOP = $arrayCFOP[$det['produto']['cfop']];
    
    //                             $cCodItInt = $det['produto']['codigo'];
    //                             $nCodProd = $det['produto']['codigo_produto'];
    //                             $codigo_local_estoque = $det['inf_adic']['codigo_local_estoque'];
    //                             $nQtd = $det['produto']['quantidade'];
    //                             $nValUnit = $det['produto']['valor_total'] / $det['produto']['quantidade'];
    
    //                             $cSitTribPIS = $det['imposto']['pis_padrao']['cod_sit_trib_pis'];
    //                             $cSitTribCOFINS = $det['imposto']['cofins_padrao']['cod_sit_trib_cofins'];
    //                             $cSitTrib = $det['imposto']['icms']['cod_sit_trib_icms'];
    //                             $cOrigem = $det['imposto']['icms']['origem_icms'];
    //                             $nAliq = $det['imposto']['icms']['aliq_icms'];
    //                             $nAliqCOFINS = $det['imposto']['cofins_padrao']['aliq_cofins'];
    //                             $nAliqPIS = $det['imposto']['pis_padrao']['aliq_pis'];
    
    //                             if (isset($arrayJuntosIguais[$cCodItInt])) {
    //                                 $arrayJuntosIguais[$cCodItInt]['nValUnit'] += $nValUnit * $det['produto']['quantidade'];
    //                                 $arrayJuntosIguais[$cCodItInt]['nQtde'] += $det['produto']['quantidade'];
    //                             } else {
    //                                 // Se não, inicialize os valores
    //                                 $arrayJuntosIguais[$cCodItInt] = array(
    //                                     'nValUnit' => $nValUnit * $det['produto']['quantidade'],
    //                                     'nQtde' => $det['produto']['quantidade'],
    //                                     'cCFOP' => $arrayCFOP[$det['produto']['cfop']],
    //                                     'nCodProd' => $det['produto']['codigo_produto'],
    //                                     'codigo_local_estoque' => $det['inf_adic']['codigo_local_estoque'],
    //                                     'PIS' => array(
    //                                         'cSitTribPIS' => $pisCofins[$det['imposto']['pis_padrao']['cod_sit_trib_pis']],
    //                                         'nAliqPIS' => $det['imposto']['pis_padrao']['aliq_pis']
    //                                     ),
    //                                     'COFINS' => array(
    //                                         'cSitTribCOFINS' => $pisCofins[$det['imposto']['cofins_padrao']['cod_sit_trib_cofins']],
    //                                         'nAliqCOFINS' => $det['imposto']['cofins_padrao']['aliq_cofins']
    //                                     ),
    //                                     'ICMS' => array(
    //                                         'cSitTrib' => $det['imposto']['icms']['cod_sit_trib_icms'],
    //                                         'cOrigem' => $det['imposto']['icms']['origem_icms'],
    //                                         'nAliq' => $det['imposto']['icms']['aliq_icms']
    //                                     )
    //                                 );
    //                             }
    //                         }else{
    //                             echo 'esse cfop não existe: '.$det['produto']['cfop'].'<br>';
    //                             break;
    //                         }
    //                     }
    //                 }

    //                 foreach ($arrayJuntosIguais as $cCodItInt => $valores) {
    //                     // Calcular valor médio
    //                     $valorMedio = $valores['nValUnit'] / $valores['nQtde'];
    //                     if($EstadoCli['estado'] == 'MG'){
    //                             $aliqicms = '';
    //                     }else{
    //                         if($valores['cCFOP'] == '1.411' ){
    //                             $aliqicms = '';
    //                         }else{
    //                             $aliqicms = ',"nAliq": "'.$valores['ICMS']['nAliq'].'"';
    //                         }
    //                     }

    //                     $aliqpis = '';
    //                     $aliqcofins = '';
    //                     if($valores['PIS']['nAliqPIS'] != '0'){ // so coloca o pis se a aliq for diferente de 0 - se mencionar com aliq zero ele da erro
    //                         $aliqpis = ',"cTpCalcPIS": "B",
    //                         "nAliqPIS": "'.$valores['PIS']['nAliqPIS'].'"';
    //                     }
    //                     if($valores['COFINS']['nAliqCOFINS'] != '0'){   // so coloca o cofins se a aliq for diferente de 0 - se mencionar com aliq zero ele da erro
    //                         $aliqcofins = ',"cTpCalcCOFINS": "B",
    //                         "nAliqCOFINS": "'.$valores['COFINS']['nAliqCOFINS'].'"';
    //                     }
    //                     // Adicionar ao JSON
    //                     $script .= '{
    //                         "cCodItInt": "'.$cCodItInt.'",
    //                         "nCodProd": '.$valores['nCodProd'].',
    //                         "codigo_local_estoque": '.$valores['codigo_local_estoque'].',
    //                         "nQtde": '.$valores['nQtde'].',
    //                         "nValUnit": '.$valorMedio.',
    //                         "cCFOP": "'.$valores['cCFOP'].'",
    //                         "PIS": {
    //                             "cSitTribPIS": "'.$valores['PIS']['cSitTribPIS'].'"
    //                             '.$aliqpis.'
    //                         },
    //                         "COFINS": {
    //                             "cSitTribCOFINS": "'.$valores['COFINS']['cSitTribCOFINS'].'"
    //                             '.$aliqcofins.'
    //                         },
    //                         "ICMS": {
    //                             "cSitTrib": "'.$valores['ICMS']['cSitTrib'].'",
    //                             "cOrigem": "'.$valores['ICMS']['cOrigem'].'"
    //                             '.$aliqicms.'
    //                         }
    //                     },';
    //                 }

    //                 $script = substr($script, 0, -1);
    //                 $script .= ']}';


    //                 $url2 = 'https://app.omie.com.br/api/v1/produtos/notaentrada/';
    //                 $headers2 = array(
    //                     'Content-type: application/json',
    //                 );

    //                 $script_array = json_decode($script, true);
    //                 $data2 = array(
    //                     'call' => 'IncluirNotaEnt',
    //                     'app_key' => $arrayCredenciaisK[$empresaAtual],
    //                     'app_secret' => $arrayCredenciaisS[$empresaAtual],
    //                     'param' => array($script_array),
    //                 );
                    
    //                 $options2 = array(
    //                     CURLOPT_URL => $url2,
    //                     CURLOPT_HTTPHEADER => $headers2,
    //                     CURLOPT_RETURNTRANSFER => true,
    //                     CURLOPT_POST => true,
    //                     CURLOPT_POSTFIELDS => json_encode($data2),
    //                 );
                
    //                 $curl2 = curl_init();
    //                 curl_setopt_array($curl2, $options2);
                
    //                 $response2 = curl_exec($curl2);
    //                 $resposta2 = json_decode($response2, true);
                

    //                 if(!isset($resposta2['faultstring'])){
    //                     $msg = "UPDATE registro_reenvio set nf_entrada = '".$resposta2['nCodNotaEnt']."', cod_nf_entrada = '".$resposta2['cCodIntNotaEnt']."' where numero_pedido_original  = '".$pedido_original."' and numero_pedido = '".$novo_pedido."';";
    //                     file_put_contents("update.txt", PHP_EOL .$msg, FILE_APPEND );
    //                     if($ConexaoREPORTS->query("UPDATE registro_reenvio set nf_entrada = '".$resposta2['nCodNotaEnt']."', cod_nf_entrada = '".$resposta2['cCodIntNotaEnt']."' where numero_pedido_original  = '".$pedido_original."' and numero_pedido = '".$novo_pedido."'")){
    //                         echo $row['numero_pedido_original'].  ' - '.$resposta2['nCodNotaEnt'].' - '.$resposta2['cCodIntNotaEnt'].' - deu certo'.PHP_EOL;
    //                     }
    //                 }else {
    //                     echo $pedido_original. ' - '.$resposta2['faultstring'].PHP_EOL;
    //                 }
    //             }else{
    //                 echo 'pedido '.$row['numero_pedido_original'].' não existe: '.$response.PHP_EOL;
    //             }
                
    //         }else{
    //             echo $pedido_original. ' Erro: não encontrou a NF, Cancelada: '.$respostachavenf['cancelada'].PHP_EOL;
    //         }
    //     }else{
    //         echo $novo_pedido. ' Erro: não encontrou a NF ou Cancelada'.PHP_EOL;
    //     }
    // }
    // ----------------------------------- fim R1 ------------------------------------

    //CONSULTA R2 - PEDIDOS COM REENVIO 2. - sem difereça de produtos. 
    $query2 = $ConexaoREPORTS->query("SELECT * FROM registro_reenvio 
    where codigo_pedido_original is not null and (codigo_pedido is not null and codigo_pedido <> '1234') and nf_entrada is null and numero_pedido like '%R2' ");
    $rows = $query2->fetchAll();
    foreach ($rows as $row) {

        $empresaAtual = $row['empresa'];
        $pedido_original = $row['numero_pedido_original'];
        $novo_pedido = $row['numero_pedido'];
        $codigo_original = $row['codigo_pedido_original'];
        $novo_codigo = $row['codigo_pedido'];

        // ---------------------- procurar NF --------------------------

        $data_sts = array(
            'call' => 'StatusPedido',
            'app_key' => $arrayCredenciaisK[$empresaAtual],
            'app_secret' => $arrayCredenciaisS[$empresaAtual],
            'param' => array(
                array('codigo_pedido' => $novo_codigo),
            ),
        );
        $optionsnf = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data_sts),
        );


    
        $curlch = curl_init();
        curl_setopt_array($curlch, $optionsnf);
    
        $chavenf = curl_exec($curlch);
        $respostachavenf0 = json_decode($chavenf, true);

       // preciso da chave nf para montar a nota de entrada.
        if(isset($respostachavenf0['ListaNfe'][0]['chave_nfe']) and $respostachavenf0['cancelada'] != 'S'){  // se a nf tiver sido cancelada, não faço nada ou se a NF nao existir.
           
            // aqui ja estou consultando a NF anterior para refazer a nota de entrada dela. 
            $query2 = $ConexaoREPORTS->query("SELECT * FROM registro_reenvio where numero_pedido = '".$pedido_original."R1'");
            $rows = $query2->fetch();
            if($rows){

                // se nao estiver cancelada, vou consultar o mesmo pedido com R1. 
                $data_sts = array(
                    'call' => 'StatusPedido',
                    'app_key' => $arrayCredenciaisK[$empresaAtual],
                    'app_secret' => $arrayCredenciaisS[$empresaAtual],
                    'param' => array(
                        array('codigo_pedido' => $rows['codigo_pedido']),
                    ),
                );
                $optionsnf = array(
                    CURLOPT_URL => $url,
                    CURLOPT_HTTPHEADER => $headers,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode($data_sts),
                );
            
                $curlch = curl_init();
                curl_setopt_array($curlch, $optionsnf);
            
                $chavenf = curl_exec($curlch);
                $respostachavenf = json_decode($chavenf, true);

        
                if(isset($respostachavenf['ListaNfe'][0]['chave_nfe']) and $respostachavenf['cancelada'] != 'S'){ 

                    // -------------------------- CONSULTAR PARA MONTAR A NF DE ENTRADA -----------------------------
                    $data = array(
                        'call' => 'ConsultarPedido',
                        'app_key' => $arrayCredenciaisK[$empresaAtual],
                        'app_secret' => $arrayCredenciaisS[$empresaAtual],
                        'param' => array(
                            array('codigo_pedido_integracao' => $rows['numero_pedido']),
                        ),
                    );

                    $options = array(
                        CURLOPT_URL => $url,
                        CURLOPT_HTTPHEADER => $headers,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_POST => true,
                        CURLOPT_POSTFIELDS => json_encode($data),
                    );

                    $curl = curl_init();
                    curl_setopt_array($curl, $options);

                    $response = curl_exec($curl);
                    $resposta = json_decode($response, true);


                    if(isset($resposta['pedido_venda_produto'])){
                        $cCodIntNotaEnt = $resposta['pedido_venda_produto']['cabecalho']['codigo_pedido'];
                        $dPrevisao = $resposta['pedido_venda_produto']['cabecalho']['data_previsao'];
                        $nCodCli = $resposta['pedido_venda_produto']['cabecalho']['codigo_cliente'];

                        $data_cliente = array(
                            'call' => 'ConsultarCliente',
                            'app_key' => $arrayCredenciaisK[$empresaAtual],
                            'app_secret' => $arrayCredenciaisS[$empresaAtual],
                            'param' => array(
                                array('codigo_cliente_omie' => $nCodCli),
                            ),
                        );
                        $option_cliente = array(
                            CURLOPT_URL => $urlcliente,
                            CURLOPT_HTTPHEADER => $headers,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => json_encode($data_cliente),
                        );
                    
                        $curlch = curl_init();
                        curl_setopt_array($curlch, $option_cliente);
                    
                        $clienteUF = curl_exec($curlch);
                        $EstadoCli = json_decode($clienteUF, true);
                        $script = '{
                            "cabec": {
                            "cCodIntNotaEnt": "'.$cCodIntNotaEnt.'",
                            "dPrevisao": "'.date('d/m/Y').'",
                            "nCodCli": "'.$nCodCli.'"
                            },
                            "infAdic": {
                            "cCodCateg": "2.09.01",
                            "cPedido": "'.$pedido_original.'R1",
                            "nfRelacionada":[{
                                "cChaveRef": "'.$respostachavenf['ListaNfe'][0]['chave_nfe'].'"
                            }]
                            },
                        
                            "produtos": [';

                        $arrayJuntosIguais = array();

                        // aqui vou montar o array buscando os itens da nota original para comparar com o novo, os itens que forem iguais, vou salvar e montar no array. 
                    if($row['motivo'] == 'produto' or $row['motivo'] == 'endereco_e_produto'){
                        // vou consultar os itens da nota atual. 
                        $datanovo = array(
                            'call' => 'ConsultarPedido',
                            'app_key' => $arrayCredenciaisK[$empresaAtual],
                            'app_secret' => $arrayCredenciaisS[$empresaAtual],
                            'param' => array(
                                array('codigo_pedido_integracao' => $novo_pedido),
                            ),
                        );
        
                        $options_novo = array(
                            CURLOPT_URL => $url,
                            CURLOPT_HTTPHEADER => $headers,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => json_encode($datanovo),
                        );
        
                        $curl_novo = curl_init();
                        curl_setopt_array($curl_novo, $options_novo);
        
                        $response_novo = curl_exec($curl_novo);
                        $resposta_novo = json_decode($response_novo, true);

                        if(isset($resposta_novo['pedido_venda_produto']['det'])){ 

                            foreach($resposta_novo['pedido_venda_produto']['det'] as $detnv){

                                foreach($resposta['pedido_venda_produto']['det'] as $det){

                                    if(isset($arrayCFOP[$detnv['produto']['cfop']]) and $arrayCFOP[$detnv['produto']['cfop']] != '' and $detnv['produto']['codigo'] == $det['produto']['codigo']){
                                   
                                        $cCFOP = $arrayCFOP[$detnv['produto']['cfop']];
            
                                        $cCodItInt = $detnv['produto']['codigo'];
                                        $nCodProd = $detnv['produto']['codigo_produto'];
                                        $codigo_local_estoque = $detnv['inf_adic']['codigo_local_estoque'];
                                        $nQtd = $detnv['produto']['quantidade'];
                                        $nValUnit = $detnv['produto']['valor_total'] / $detnv['produto']['quantidade'];
            
                                        $cSitTribPIS = $det['imposto']['pis_padrao']['cod_sit_trib_pis'];
                                        $cSitTribCOFINS = $det['imposto']['cofins_padrao']['cod_sit_trib_cofins'];
                                        $cSitTrib = $det['imposto']['icms']['cod_sit_trib_icms'];
                                        $cOrigem = $det['imposto']['icms']['origem_icms'];
                                        $nAliq = $det['imposto']['icms']['aliq_icms'];
                                        $nAliqCOFINS = $det['imposto']['cofins_padrao']['aliq_cofins'];
                                        $nAliqPIS = $det['imposto']['pis_padrao']['aliq_pis'];
            
                                        if (isset($arrayJuntosIguais[$cCodItInt])) {
                                            $arrayJuntosIguais[$cCodItInt]['nValUnit'] += $nValUnit * $detnv['produto']['quantidade'];
                                            $arrayJuntosIguais[$cCodItInt]['nQtde'] += $detnv['produto']['quantidade'];
                                        } else {
                                            // Se não, inicialize os valores
                                            $arrayJuntosIguais[$cCodItInt] = array(
                                                'nValUnit' => $nValUnit * $detnv['produto']['quantidade'],
                                                'nQtde' => $detnv['produto']['quantidade'],
                                                'cCFOP' => $arrayCFOP[$detnv['produto']['cfop']],
                                                'nCodProd' => $detnv['produto']['codigo_produto'],
                                                'codigo_local_estoque' => $detnv['inf_adic']['codigo_local_estoque'],
                                                'PIS' => array(
                                                    'cSitTribPIS' => $pisCofins[$detnv['imposto']['pis_padrao']['cod_sit_trib_pis']],
                                                    'nAliqPIS' => $detnv['imposto']['pis_padrao']['aliq_pis']
                                                ),
                                                'COFINS' => array(
                                                    'cSitTribCOFINS' => $pisCofins[$detnv['imposto']['cofins_padrao']['cod_sit_trib_cofins']],
                                                    'nAliqCOFINS' => $detnv['imposto']['cofins_padrao']['aliq_cofins']
                                                ),
                                                'ICMS' => array(
                                                    'cSitTrib' => $detnv['imposto']['icms']['cod_sit_trib_icms'],
                                                    'cOrigem' => $detnv['imposto']['icms']['origem_icms'],
                                                    'nAliq' => $detnv['imposto']['icms']['aliq_icms']
                                                )
                                            );
                                        }
                                    }else if(isset($arrayCFOP[$detnv['produto']['cfop']]) and $arrayCFOP[$detnv['produto']['cfop']] != '' and strstr($det['produto']['descricao'], 'Assinatura')){
                                        $cCFOP = $arrayCFOP[$detnv['produto']['cfop']];
            
                                        $cCodItInt = $det['produto']['codigo'];
                                        $nCodProd = $det['produto']['codigo_produto'];
                                        $codigo_local_estoque = $detnv['inf_adic']['codigo_local_estoque'];
                                        $nQtd = $detnv['produto']['quantidade'];
                                        $nValUnit = $detnv['produto']['valor_total'] / $detnv['produto']['quantidade'];
            
                                        $cSitTribPIS = $det['imposto']['pis_padrao']['cod_sit_trib_pis'];
                                        $cSitTribCOFINS = $det['imposto']['cofins_padrao']['cod_sit_trib_cofins'];
                                        $cSitTrib = $det['imposto']['icms']['cod_sit_trib_icms'];
                                        $cOrigem = $det['imposto']['icms']['origem_icms'];
                                        $nAliq = $det['imposto']['icms']['aliq_icms'];
                                        $nAliqCOFINS = $det['imposto']['cofins_padrao']['aliq_cofins'];
                                        $nAliqPIS = $det['imposto']['pis_padrao']['aliq_pis'];
            
                                        if (isset($arrayJuntosIguais[$cCodItInt])) {
                                            $arrayJuntosIguais[$cCodItInt]['nValUnit'] += $nValUnit * $detnv['produto']['quantidade'];
                                            $arrayJuntosIguais[$cCodItInt]['nQtde'] += $detnv['produto']['quantidade'];
                                        } else {
                                            // Se não, inicialize os valores
                                            $arrayJuntosIguais[$cCodItInt] = array(
                                                'nValUnit' => $nValUnit * $detnv['produto']['quantidade'],
                                                'nQtde' => $detnv['produto']['quantidade'],
                                                'cCFOP' => $arrayCFOP[$detnv['produto']['cfop']],
                                                'nCodProd' => $detnv['produto']['codigo_produto'],
                                                'codigo_local_estoque' => $detnv['inf_adic']['codigo_local_estoque'],
                                                'PIS' => array(
                                                    'cSitTribPIS' => $pisCofins[$detnv['imposto']['pis_padrao']['cod_sit_trib_pis']],
                                                    'nAliqPIS' => $detnv['imposto']['pis_padrao']['aliq_pis']
                                                ),
                                                'COFINS' => array(
                                                    'cSitTribCOFINS' => $pisCofins[$detnv['imposto']['cofins_padrao']['cod_sit_trib_cofins']],
                                                    'nAliqCOFINS' => $detnv['imposto']['cofins_padrao']['aliq_cofins']
                                                ),
                                                'ICMS' => array(
                                                    'cSitTrib' => $detnv['imposto']['icms']['cod_sit_trib_icms'],
                                                    'cOrigem' => $detnv['imposto']['icms']['origem_icms'],
                                                    'nAliq' => $detnv['imposto']['icms']['aliq_icms']
                                                )
                                            );
                                        }
                                    }else if(isset($arrayCFOP[$detnv['produto']['cfop']]) and $arrayCFOP[$detnv['produto']['cfop']] != '' and $detnv['produto']['codigo'] != $det['produto']['codigo']){
                                        $cCFOP = $arrayCFOP[$detnv['produto']['cfop']];
            
                                        $cCodItInt = $det['produto']['codigo'];
                                        $nCodProd = $det['produto']['codigo_produto'];
                                        $codigo_local_estoque = $detnv['inf_adic']['codigo_local_estoque'];
                                        $nQtd = $detnv['produto']['quantidade'];
                                        $nValUnit = $detnv['produto']['valor_total'] / $detnv['produto']['quantidade'];
            
                                        $cSitTribPIS = $det['imposto']['pis_padrao']['cod_sit_trib_pis'];
                                        $cSitTribCOFINS = $det['imposto']['cofins_padrao']['cod_sit_trib_cofins'];
                                        $cSitTrib = $det['imposto']['icms']['cod_sit_trib_icms'];
                                        $cOrigem = $det['imposto']['icms']['origem_icms'];
                                        $nAliq = $det['imposto']['icms']['aliq_icms'];
                                        $nAliqCOFINS = $det['imposto']['cofins_padrao']['aliq_cofins'];
                                        $nAliqPIS = $det['imposto']['pis_padrao']['aliq_pis'];
            
                                        if (isset($arrayJuntosIguais[$cCodItInt])) {
                                            $arrayJuntosIguais[$cCodItInt]['nValUnit'] += $nValUnit * $detnv['produto']['quantidade'];
                                            $arrayJuntosIguais[$cCodItInt]['nQtde'] += $detnv['produto']['quantidade'];
                                        } else {
                                            // Se não, inicialize os valores
                                            $arrayJuntosIguais[$cCodItInt] = array(
                                                'nValUnit' => $nValUnit * $detnv['produto']['quantidade'],
                                                'nQtde' => $detnv['produto']['quantidade'],
                                                'cCFOP' => $arrayCFOP[$detnv['produto']['cfop']],
                                                'nCodProd' => $detnv['produto']['codigo_produto'],
                                                'codigo_local_estoque' => $detnv['inf_adic']['codigo_local_estoque'],
                                                'PIS' => array(
                                                    'cSitTribPIS' => $pisCofins[$detnv['imposto']['pis_padrao']['cod_sit_trib_pis']],
                                                    'nAliqPIS' => $detnv['imposto']['pis_padrao']['aliq_pis']
                                                ),
                                                'COFINS' => array(
                                                    'cSitTribCOFINS' => $pisCofins[$detnv['imposto']['cofins_padrao']['cod_sit_trib_cofins']],
                                                    'nAliqCOFINS' => $detnv['imposto']['cofins_padrao']['aliq_cofins']
                                                ),
                                                'ICMS' => array(
                                                    'cSitTrib' => $detnv['imposto']['icms']['cod_sit_trib_icms'],
                                                    'cOrigem' => $detnv['imposto']['icms']['origem_icms'],
                                                    'nAliq' => $detnv['imposto']['icms']['aliq_icms']
                                                )
                                            );
                                        }
                                    }
                                }
                                
                            }
                        }
                    }else{  // aqui são os pedidos que possuem todos os itens iguais, nao farei a tratativa. 
                        
                        foreach($resposta['pedido_venda_produto']['det'] as $det){
                            if(isset($arrayCFOP[$det['produto']['cfop']]) and $arrayCFOP[$det['produto']['cfop']] != ''){
                                $cCFOP = $arrayCFOP[$det['produto']['cfop']];

                                $cCodItInt = $det['produto']['codigo'];
                                $nCodProd = $det['produto']['codigo_produto'];
                                $codigo_local_estoque = $det['inf_adic']['codigo_local_estoque'];
                                $nQtd = $det['produto']['quantidade'];
                                $nValUnit = $det['produto']['valor_total'] / $det['produto']['quantidade'];

                                $cSitTribPIS = $det['imposto']['pis_padrao']['cod_sit_trib_pis'];
                                $cSitTribCOFINS = $det['imposto']['cofins_padrao']['cod_sit_trib_cofins'];
                                $cSitTrib = $det['imposto']['icms']['cod_sit_trib_icms'];
                                $cOrigem = $det['imposto']['icms']['origem_icms'];
                                $nAliq = $det['imposto']['icms']['aliq_icms'];
                                $nAliqCOFINS = $det['imposto']['cofins_padrao']['aliq_cofins'];
                                $nAliqPIS = $det['imposto']['pis_padrao']['aliq_pis'];

                                if (isset($arrayJuntosIguais[$cCodItInt])) {
                                    $arrayJuntosIguais[$cCodItInt]['nValUnit'] += $nValUnit * $det['produto']['quantidade'];
                                    $arrayJuntosIguais[$cCodItInt]['nQtde'] += $det['produto']['quantidade'];
                                } else {
                                    // Se não, inicialize os valores
                                    $arrayJuntosIguais[$cCodItInt] = array(
                                        'nValUnit' => $nValUnit * $det['produto']['quantidade'],
                                        'nQtde' => $det['produto']['quantidade'],
                                        'cCFOP' => $arrayCFOP[$det['produto']['cfop']],
                                        'nCodProd' => $det['produto']['codigo_produto'],
                                        'codigo_local_estoque' => $det['inf_adic']['codigo_local_estoque'],
                                        'PIS' => array(
                                            'cSitTribPIS' => $pisCofins[$det['imposto']['pis_padrao']['cod_sit_trib_pis']],
                                            'nAliqPIS' => $det['imposto']['pis_padrao']['aliq_pis']
                                        ),
                                        'COFINS' => array(
                                            'cSitTribCOFINS' => $pisCofins[$det['imposto']['cofins_padrao']['cod_sit_trib_cofins']],
                                            'nAliqCOFINS' => $det['imposto']['cofins_padrao']['aliq_cofins']
                                        ),
                                        'ICMS' => array(
                                            'cSitTrib' => $det['imposto']['icms']['cod_sit_trib_icms'],
                                            'cOrigem' => $det['imposto']['icms']['origem_icms'],
                                            'nAliq' => $det['imposto']['icms']['aliq_icms']
                                        )
                                    );
                                }
                            }else{
                                echo 'esse cfop não existe: '.$det['produto']['cfop'].'<br>';
                                break;
                            }
                        }
                    }
                    foreach ($arrayJuntosIguais as $cCodItInt => $valores) {
                        // Calcular valor médio
                        $valorMedio = $valores['nValUnit'] / $valores['nQtde'];
                        if($EstadoCli['estado'] == 'MG'){
                                $aliqicms = '';
                        }else{
                            if($valores['cCFOP'] == '1.411' ){
                                $aliqicms = '';
                            }else{
                                $aliqicms = ',"nAliq": "'.$valores['ICMS']['nAliq'].'"';
                            }
                        }

                        $aliqpis = '';
                        $aliqcofins = '';
                        if($valores['PIS']['nAliqPIS'] != '0'){ // so coloca o pis se a aliq for diferente de 0 - se mencionar com aliq zero ele da erro
                            $aliqpis = ',"cTpCalcPIS": "B",
                            "nAliqPIS": "'.$valores['PIS']['nAliqPIS'].'"';
                        }
                        if($valores['COFINS']['nAliqCOFINS'] != '0'){   // so coloca o cofins se a aliq for diferente de 0 - se mencionar com aliq zero ele da erro
                            $aliqcofins = ',"cTpCalcCOFINS": "B",
                            "nAliqCOFINS": "'.$valores['COFINS']['nAliqCOFINS'].'"';
                        }
                        // Adicionar ao JSON
                        $script .= '{
                            "cCodItInt": "'.$cCodItInt.'",
                            "nCodProd": '.$valores['nCodProd'].',
                            "codigo_local_estoque": '.$valores['codigo_local_estoque'].',
                            "nQtde": '.$valores['nQtde'].',
                            "nValUnit": '.$valorMedio.',
                            "cCFOP": "'.$valores['cCFOP'].'",
                            "PIS": {
                                "cSitTribPIS": "'.$valores['PIS']['cSitTribPIS'].'"
                                '.$aliqpis.'
                            },
                            "COFINS": {
                                "cSitTribCOFINS": "'.$valores['COFINS']['cSitTribCOFINS'].'"
                                '.$aliqcofins.'
                            },
                            "ICMS": {
                                "cSitTrib": "'.$valores['ICMS']['cSitTrib'].'",
                                "cOrigem": "'.$valores['ICMS']['cOrigem'].'"
                                '.$aliqicms.'
                            }
                        },';
                    }


                        $script = substr($script, 0, -1);
                        $script .= ']}';

                        $url2 = 'https://app.omie.com.br/api/v1/produtos/notaentrada/';
                        $headers2 = array(
                            'Content-type: application/json',
                        );

                        $script_array = json_decode($script, true);
                        $data2 = array(
                            'call' => 'IncluirNotaEnt',
                            'app_key' => $arrayCredenciaisK[$empresaAtual],
                            'app_secret' => $arrayCredenciaisS[$empresaAtual],
                            'param' => array($script_array),
                        );
                        
                        $options2 = array(
                            CURLOPT_URL => $url2,
                            CURLOPT_HTTPHEADER => $headers2,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => json_encode($data2),
                        );
                    
                        $curl2 = curl_init();
                        curl_setopt_array($curl2, $options2);
                    
                        $response2 = curl_exec($curl2);
                        $resposta2 = json_decode($response2, true);
                    
                        if(!isset($resposta2['faultstring'])){

                            $msg = "UPDATE registro_reenvio set nf_entrada = '".$resposta2['nCodNotaEnt']."', cod_nf_entrada = '".$resposta2['cCodIntNotaEnt']."' where numero_pedido_original  = '".$pedido_original."' and numero_pedido = '".$novo_pedido."';";
                            file_put_contents("update.txt", PHP_EOL .$msg, FILE_APPEND );
                            echo "UPDATE registro_reenvio set nf_entrada = '".$resposta2['nCodNotaEnt']."', cod_nf_entrada = '".$resposta2['cCodIntNotaEnt']."' where numero_pedido_original  = '".$pedido_original."' and numero_pedido = '".$novo_pedido."';".PHP_EOL;
                            if($ConexaoREPORTS->query("UPDATE registro_reenvio set nf_entrada = '".$resposta2['nCodNotaEnt']."', cod_nf_entrada = '".$resposta2['cCodIntNotaEnt']."' where numero_pedido_original  = '".$pedido_original."' and numero_pedido = '".$novo_pedido."'")){
                                echo $row['numero_pedido_original'].  ' - '.$resposta2['nCodNotaEnt'].' - '.$resposta2['cCodIntNotaEnt'].' - deu certo'.PHP_EOL;
                            }
                        }else {
                            echo $pedido_original. ' - '.$resposta2['faultstring'].PHP_EOL;
                        }
                    }else{
                        echo 'pedido '.$row['numero_pedido_original'].' não existe: '.$response.PHP_EOL;
                    }
                }else{
                    echo $pedido_original. ' Erro: não encontrou a NF, Cancelada: '.PHP_EOL;
                }
            }
        }else{
            echo $novo_pedido. ' Erro: não encontrou a NF ou Cancelada'.PHP_EOL;
        }
    }
    //----------------------------------- fim R2 ------------------------------------
}

function faturar_tudo(){

    $ConexaoREPORTS = ConexaoMYSQL_reports::getConnection();
        
    $arrayCredenciaisK = array(
        'CRECENCIALKEY' => '11111111111',
    );
    $arrayCredenciaisS = array(
        'CREDENCIALSECRET' => '111111111111111111111111111111111111111'
    );

    $url2 = 'https://app.omie.com.br/api/v1/produtos/notaentradafat/';
    $headers2 = array(
        'Content-type: application/json',
    );

    $urlConferir = 'https://app.omie.com.br/api/v1/produtos/notaentrada/';
    

    $query2 = $ConexaoREPORTS->query("SELECT * FROM registro_reenvio left join nf_entrada_faturada on nfe_entrada = nf_entrada and cod_nf_entrada where idnf_id is null and length(nf_entrada) > 7");
    $rows = $query2->fetchAll();
    foreach ($rows as $row) {

            $empresaAtual = $row['empresa'];
            $script = '{
                "nCodNotaEnt": '.$row['nf_entrada'].',
                "cCodIntNotaEnt": "'.$row['cod_nf_entrada'].'"
            }';
            
            $script_array = json_decode($script, true);
            $data2 = array(
                'call' => 'ConcluirNotaEnt',
                'app_key' => $arrayCredenciaisK[$empresaAtual],
                'app_secret' => $arrayCredenciaisS[$empresaAtual],
                'param' => array($script_array),
            );
            
            $options2 = array(
                CURLOPT_URL => $url2,
                CURLOPT_HTTPHEADER => $headers2,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($data2),
            );
        
            $curl2 = curl_init();
            curl_setopt_array($curl2, $options2);
        
            $response2 = curl_exec($curl2);
            $resposta2 = json_decode($response2, true);

            if(!isset($resposta2['faultstring'])){
                if(isset($resposta2['cDescStatus'])){
                    if($ConexaoREPORTS->query("INSERT IGNORE INTO nf_entrada_faturada set nfe_entrada = '".$row['nf_entrada']."', nfe_cod_entrada = '".$row['cod_nf_entrada']."', nfe_status = '".mb_convert_encoding($resposta2['cDescStatus'], 'Windows-1252', 'UTF-8')."'")){
                        echo $row['numero_pedido'].' - '.mb_convert_encoding($resposta2['cDescStatus'], 'Windows-1252', 'UTF-8').PHP_EOL;
                    }
                }else{
                    echo $row['numero_pedido'].' - '.$response2.PHP_EOL;
                }
            }else{
                
                if(strstr(mb_convert_encoding($resposta2['faultstring'], 'Windows-1252', 'UTF-8'), 'Nota de Entrada foi conclu')){
                    $dataconsultar = array(
                        'call' => 'ConsultarNotaEnt',
                        'app_key' => $arrayCredenciaisK[$empresaAtual],
                        'app_secret' => $arrayCredenciaisS[$empresaAtual],
                        'param' => array($script_array),
                    );
                    $options2 = array(
                        CURLOPT_URL => $urlConferir,
                        CURLOPT_HTTPHEADER => $headers2,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_POST => true,
                        CURLOPT_POSTFIELDS => json_encode($data2),
                    );
                
                    $curl2 = curl_init();
                    curl_setopt_array($curl2, $options2);
                
                    $responsecon = curl_exec($curl2);
                    $respostacon = json_decode($responsecon, true);

      
                    if(!empty($respostacon['cabec']['cNumeroNotaEnt'])){
                        if($ConexaoREPORTS->query("INSERT IGNORE INTO nf_entrada_faturada set nfe_entrada = '".$row['nf_entrada']."', nfe_cod_entrada = '".$row['cod_nf_entrada']."', nfe_status = '".mb_convert_encoding('Concluida (checagem manual)', 'Windows-1252', 'UTF-8')."'")){
                            echo $row['numero_pedido'].' - '.$respostacon['cabec']['cNumeroNotaEnt'].PHP_EOL;
                        }

                    }
                   
                }
                echo $row['numero_pedido'].' - '.mb_convert_encoding($resposta2['faultstring'], 'Windows-1252', 'UTF-8').PHP_EOL;
            }
            
    }
}

function checarNF(){
    $ConexaoREPORTS = ConexaoMYSQL_reports::getConnection();
        
    $arrayCredenciaisK = array(
        'CRECENCIALKEY' => '11111111111',
    );
    $arrayCredenciaisS = array(
        'CREDENCIALSECRET' => '111111111111111111111111111111111111111'
    );
   

    $url2 = 'https://app.omie.com.br/api/v1/produtos/notaentrada/';
    $headers2 = array(
        'Content-type: application/json',
    );

    $array_pedidos = array();
    $query2 = $ConexaoREPORTS->query("SELECT * FROM registro_reenvio 
    where codigo_pedido_original is not null and (codigo_pedido is not null and codigo_pedido <> '1234' and length(codigo_pedido) < 15) and  numero_pedido like '%R1' AND nf_entrada is null");
    $rows = $query2->fetchAll();
    foreach ($rows as $row) {
       $array_pedidos[] = $row['numero_pedido_original'];
    }

    $empresaAtual = 'alterar aqui a empresa';
    $totalpagina = 1; // Inicializa total de páginas

    for ($pagina = 1; $pagina <= $totalpagina; $pagina++) {

        $script = '{
            "nPagina": ' . $pagina . ',
            "nRegistrosPorPagina": 100
        }';

        $script_array = json_decode($script, true);
        $data2 = array(
            'call' => 'ListarNotaEnt',
            'app_key' => $arrayCredenciaisK[$empresaAtual],
            'app_secret' => $arrayCredenciaisS[$empresaAtual],
            'param' => array($script_array),
        );

        $options2 = array(
            CURLOPT_URL => $url2,
            CURLOPT_HTTPHEADER => $headers2,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data2),
        );

        $curl2 = curl_init();
        curl_setopt_array($curl2, $options2);
        $response2 = curl_exec($curl2);
        $resposta = json_decode($response2, true);

        // Atualiza o total de páginas se for diferente
        if (isset($resposta['nTotalPaginas'])) {
            $totalpagina = $resposta['nTotalPaginas'];
        }

        foreach ($resposta['notas'] as $nf) {
            $pedidonf = $nf['infAdic']['cPedido'];
            echo $pedidonf.PHP_EOL;
            if (in_array($pedidonf, $array_pedidos)) {
                $msg = "UPDATE registro_reenvio set nf_entrada = '" . $nf['cabec']['nCodNotaEnt'] . "', cod_nf_entrada = '" . $nf['cabec']['cCodIntNotaEnt'] . "' where numero_pedido_original  = '" . $pedidonf . "' and numero_pedido = '" . $pedidonf . "R1';";
                file_put_contents("update.txt", PHP_EOL . $msg, FILE_APPEND);
            }
        }
    }

}
