<?php

use Mpdf\Utils\Arrays;

include_once("sessao.php");
include('global_assets/php/conexao.php');


if (!empty($_POST['inputProdutoId'])) {
    if (session_status() !== 'PHP_SESSION_ACTIVE') {

        //$produtos = [];

        //if(isset($_SESSION['carrinho'])) {
        //  if(!in_array($_SESSION['carrinho'], $produtos)){
        //     array_push($produtos, $_SESSION['carrinho']);
        // } else {
        //     print('ja existe');
        // }
        // }
        $verifc = false;

        // Esse trecho de código é executado se o array $_SESSION possuir o indice 
        // Carrinho.
        if (isset($_SESSION['Carrinho'])) {
            $produtos = $_SESSION['Carrinho']; // O array temporário $produtos recebe Carrinho

            // Neste foreach é verificado se o id do produto que etá sendo adicionado já está 
            // no array produtos.
            foreach ($produtos as $item) {
                if ($_POST['inputProdutoId'] == $item['id']) {
                    $verifc = true;
                }
            }

            // Caso $verifc seja falso, o id que está vindo na req POST é novo,
            // então é adicionado ao array $produtos.
            if ($verifc == false) {
                array_push($produtos, ['quantidade' => 1, 'id' => $_POST['inputProdutoId']]);

                //Carregar o no item na tela de modal do carrinho da pagina de solicitação
                $sql = "SELECT ProduId, ProduCodigo, ProduNome, ProduFoto, CategNome
                        FROM Produto
                        JOIN Categoria on CategId = ProduCategoria
                        WHERE ProduId = " . $_POST['inputProdutoId'] . " and ProduEmpresa = " . $_SESSION['EmpreId'] . " and ProduStatus = 1
                        ";
                $result = $conn->query($sql);
                $row = $result->fetch(PDO::FETCH_ASSOC);

                print('
							<div class="custon-modal-produto">
							<div class="custon-modal-produTitle d-flex flex-column col-12 col-sm-9">
								<p>' . $row['ProduNome'] . '</p>
								<p>' . $row['CategNome'] . '</p>
							</div>
							<div class="col-12 col-sm-3 row justify-content-md-center align-items-center mx-0">
								<div class="input-group bootstrap-touchspin">
									<span class="input-group-prepend">
										<button id="' . $row['ProduId'] . '" class="btn btn-light bootstrap-touchspin-down quant-edit" type="button">–</button>
									</span>
									<span class="input-group-prepend bootstrap-touchspin-prefix d-none">
										<span class="input-group-text"></span>
									</span>
									<input idProdu="' . $row['ProduId'] . '" style="text-align: center" type="text" value="' . 1 . '" class="form-control touchspin-set-value" style="display: block;">
									<span class="input-group-append bootstrap-touchspin-postfix d-none">
										<span class="input-group-text"></span>
									</span>
									<span class="input-group-append">
										<button id="' . $row['ProduId'] . '" class="btn btn-light bootstrap-touchspin-up quant-edit" type="button">+</button>
									</span>
								</div>
							</div>
						</div>
							
							     ');
            } else {
                print('Produto já existente.');
            }

            // $_SESSION['Carrinho'] recebe o array atualizado com a nova posição.
            $_SESSION['Carrinho'] = $produtos;
        } else {

            // Se o indice Carrinho não existir em $_SESSION, é inicializado com o primeiro 
            // valor vindo no POST.
            $_SESSION['Carrinho'] = [['quantidade' => 1, 'id' => $_POST['inputProdutoId']]];


            //Carregar o no item na tela de modal do carrinho da pagina de solicitação
            $sql = "SELECT ProduId, ProduCodigo, ProduNome, ProduFoto, CategNome
                    FROM Produto
                    JOIN Categoria on CategId = ProduCategoria
                    WHERE ProduId = " . $_POST['inputProdutoId'] . " and ProduEmpresa = " . $_SESSION['EmpreId'] . " and ProduStatus = 1
                    ";
            $result = $conn->query($sql);
            $row = $result->fetch(PDO::FETCH_ASSOC);

            print('
							<div class="custon-modal-produto">
							<div class="custon-modal-produTitle d-flex flex-column col-12 col-sm-9">
								<p>' . $row['ProduNome'] . '</p>
								<p>' . $row['CategNome'] . '</p>
							</div>
							<div class="col-12 col-sm-3 row justify-content-md-center align-items-center mx-0">
								<div class="input-group bootstrap-touchspin">
									<span class="input-group-prepend">
										<button id="' . $row['ProduId'] . '" class="btn btn-light bootstrap-touchspin-down quant-edit" type="button">–</button>
									</span>
									<span class="input-group-prepend bootstrap-touchspin-prefix d-none">
										<span class="input-group-text"></span>
									</span>
									<input idProdu="' . $row['ProduId'] . '" style="text-align: center" type="text" value="' . 1 . '" class="form-control touchspin-set-value" style="display: block;">
									<span class="input-group-append bootstrap-touchspin-postfix d-none">
										<span class="input-group-text"></span>
									</span>
									<span class="input-group-append">
										<button id="' . $row['ProduId'] . '" class="btn btn-light bootstrap-touchspin-up quant-edit" type="button">+</button>
									</span>
								</div>
							</div>
						</div>
							
							     ');
        }

        //unset($_SESSION['Carrinho']);
    }
}
