<?php 
include 'conexao/bancodedados.php';
$Conexao = ConexaoMYSQL_prod::getConnection();

$arrayPedidos = array();

EnviaOmie('empresa', $pedido);



function consultaOmie($url, $req){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

function EnviaOmie($empresa, $pedido){
      

    $queryOmie = $Conexao->query("SELECT * FROM TOKEN_OMIE where empresa = '".$empresa."'");
    $rowOmie = $queryOmie->fetch();
    if($rowOmie){

        $api_key = $rowOmie['api_key'];
        $api_secret = $rowOmie['api_secret'];


        $pedido_banco = $Conexao->query("SELECT * FROM PEDIDO where PEDIDO = '".$pedido."'");
        $rowPedido = $pedido_banco->fetch();
        if($rowPedido){
            $endereco = $rowPedido['pedidos_entrega_endereco'];
            $endereco_numero = $rowPedido['pedidos_entrega_numero'];
            $bairro = $rowPedido['pedidos_entrega_bairro'];
            $complemento = $rowPedido['pedidos_entrega_complemento'];
            $estado = $rowPedido['pedidos_entrega_uf'];
            $cidade = $rowPedido['pedidos_entrega_cidade'];
            $cep = $rowPedido['pedidos_entrega_cep'];
            $numero_pedido_cliente = $rowPedido['pedidos_cliente_celular'];

            if (strpos($numero_pedido_cliente, '(') === false) {
                // Extrai o DDD e o número
                $ddd = substr($numero_pedido_cliente, 0, 2);
                $numero_pedido_cliente = substr($numero_pedido_cliente, 2);
    
                // Adiciona a máscara ao número
                $numero_pedido_cliente = "($ddd) $numero_pedido_cliente";
            }
            // Se precisar separar DDD e número, pode fazer assim:
            $telefone = explode(' ', $numero_pedido_cliente);
            $ddd = $telefone[0];
            $numero_telefone = $telefone[1];

            echo $rowPedido['pedidos_cliente_nome'].' | '; 

            $criaCliente = json_encode(mb_convert_encoding([
                'call' => 'UpsertCliente',
                'app_key' => "$api_key",
                'app_secret' => "$api_secret",
                'param' => [
                    [
                        'codigo_cliente_integracao' => $rowPedido['pedidos_cliente_cpfCnpj'],
                        'email' => $rowPedido['pedidos_cliente_email'],
                        'razao_social' => $rowPedido['pedidos_cliente_nome'],
                        'nome_fantasia' => $rowPedido['pedidos_cliente_nome'],
                        'cnpj_cpf' => $rowPedido['pedidos_cliente_cpfCnpj'],
                        'endereco' => $endereco,
                        'endereco_numero' => $endereco_numero,
                        'bairro' => $bairro,
                        'complemento' => $complemento,
                        'estado' => $estado,
                        'cidade' => $cidade,
                        'cep' => $cep,
                        'optante_simples_nacional' => 'N',
                        'contribuinte' => 'N',
                        'telefone1_ddd' => str_replace(['(', ')'], '', $ddd),
                        'telefone1_numero' => str_replace('-', '', $numero_telefone),
                    ]
                ]
            ], 'UTF-8', 'ISO-8859-1'));

            if ($criaCliente === false) {
                echo 'Erro ao codificar JSON: ' . json_last_error_msg(); die;
            }

            $resultadoCliente = json_decode(consultaOmie('https://app.omie.com.br/api/v1/geral/clientes/', $criaCliente));

            if (!isset($resultadoCliente->codigo_cliente_omie) && isset($resultadoCliente->faultstring)) {
                echo 'error: Você já aprovou esta requisição.'; die;
            }

            $valor_frete = 0;

            $queryPedidos = $Conexao->query("SELECT * FROM pedido
            where pedido = '".$pedido."'");
            while($rowPed = $queryPedidos->fetch()){
                $pedido_reenvio[] = $rowPed;
            }
                
            foreach ($pedido_reenvio as $key => $produto) {
                
                $produtos[] =
                    [
                        "ide" => [
                            "codigo_item_integracao" => $key
                        ],
                        "inf_adic" => [
                            "nao_gerar_financeiro" => "S"
                        ],
                        "produto" => [
                            "codigo_produto_integracao" => $produto['id_produto'],
                            "descricao" => $produto['nome_produto'],
                            "quantidade" => $produto['pedido_iten_qtde'],
                            "unidade" => "UN",
                            "valor_unitario" => $produto['valor_unitario']
                        ]
                    ];

                $valor_frete += $produto['frete_unitario'];
                
            }
        
            $transportado = 0;
            $codVend = 0;
            $codigo_conta_corrente = 0;
            $codigo_cenario_impostos = '';
            $projeto = 0;

			$transportado = 1111111;

			$codVend = 1111111;
			$codigo_conta_corrente = 11111111;
			$codigo_cenario_impostos = '111111';
			$projeto = 11111111;

            //Monta o json para incluir o pedido na omie, baseado no cliente
            $Arraypedido = [
                'call' => 'IncluirPedido',
                'app_key' => "$api_key",
                'app_secret' => "$api_secret",
                'param' => [
                    [
                        'cabecalho' =>
                        [
                            "codigo_cliente" => $resultadoCliente->codigo_cliente_omie,
                            "codigo_pedido_integracao" => $pedidoNovo,
                            "data_previsao" => date('d/m/Y', strtotime('+1 days')),
                            "etapa" => "10",
                            "codigo_parcela" => "000",
                            "codigo_cenario_impostos" => "$codigo_cenario_impostos"
                        ],
                        'frete' =>
                        [
                            "codigo_transportadora" => $transportado,
                            "valor_frete" => $valor_frete,
                            "modalidade" => 0
                        ],
                        "det" => $produtos,
                        "informacoes_adicionais" =>  [
                            "codigo_categoria" =>  "1.01.03",
                            "codProj" => $projeto,
                            "numero_pedido_cliente" => $pedidoNovo,
                            "codVend" => $codVend,
                            "codigo_conta_corrente" => $codigo_conta_corrente,
                            "consumidor_final" =>  "S",
                            "enviar_email" =>  "N"
                        ],
                    ]
                ]
            ];

            $desconto = $rowPedido['pedidos_valores_descontoAdicional'] + $rowPedido['pedidos_valores_descontoCupom'];

            if ($desconto > 0) {

                $Arraypedido['param'][0]['cabecalho']['tipo_desconto_pedido']   = "V";
                $Arraypedido['param'][0]['cabecalho']['valor_desconto_pedido']  = $desconto;
            }

            $incluirPedido = json_encode($Arraypedido);

            $pedido = json_decode(consultaOmie('https://app.omie.com.br/api/v1/produtos/pedido/', $incluirPedido));

            if (!isset($pedido->faultstring)) {

                $faturarPedido = [
                    "nCodPed" => $pedido->codigo_pedido
                ];

                $req = json_encode([
                    'call' => 'FaturarPedidoVenda',
                    'app_key' => "$api_key",
                    'app_secret' => "$api_secret",
                    'param' => [$faturarPedido]
                ]);

                $response = consultaOmie('https://app.omie.com.br/api/v1/produtos/pedidovendafat/', $req);

                $Conexao_reports->query("UPDATE registro_reenvio 
                SET reenviado = '1', data_h_reenvio = '".date('Y-m-d H:i:s')."' where numero_pedido = '".$pedidoNovo."'");

                echo 'Reenvio aprovada com sucesso. :'.$response.PHP_EOL;
            } else {
                echo 'error '.$pedido->faultstring.PHP_EOL;
            }
        }
    }
}