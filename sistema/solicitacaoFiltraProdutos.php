<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

function queryPesquisa()
{

    $cont = 0;

    include('global_assets/php/conexao.php');

    $args = [];

    if (!empty($_POST['inputPesquisaProduto'])) {
        $args[]  = "ProduNome LIKE '%" . $_POST['inputPesquisaProduto'] . "%'";
    }

    if (!empty($_POST['inputCategoria'])) {
        $args[]  = "ProduCategoria = " . $_POST['inputCategoria'] . " ";
    }

    if (!empty($_POST['inputSubCategoria'])) {
        $args[]  = "ProduSubCategoria = " . $_POST['inputSubCategoria'] . " ";
    }

    if (!empty($_POST['inputMarca'])) {
        $args[]  = "ProduMarca = " . $_POST['inputMarca'] . " ";
    }

    if (!empty($_POST['inputFabricante'])) {
        $args[]  = "ProduFabricante = " . $_POST['inputFabricante'] . " ";
    }

    if (!empty($_POST['inputModelo'])) {
        $args[]  = "ProduModelo = " . $_POST['inputModelo'] . " ";
    }


    if (count($args) >= 1) {
        try {

            $string = implode(" and ", $args);

            if ($string != '') {
                $string .= ' and ';
            }

            $sql = "SELECT ProduId, ProduCodigo, ProduNome, ProduFoto, CategNome, dbo.fnSaldoEstoque(ProduEmpresa, ProduId, NULL) as Estoque
                    FROM Produto
                    JOIN Categoria on CategId = ProduCategoria
                    JOIN Situacao on SituaId = ProduStatus
                    WHERE " . $string . " ProduEmpresa = " . $_SESSION['EmpreId'] . " and SituaChave = 'ATIVO' 
                    ";
            $result = $conn->query("$sql");
            $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

            count($rowData) >= 1 ? $cont = 1 : $cont = 0;
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    } else {
        try {

            $sql = "SELECT ProduId, ProduCodigo, ProduNome, ProduFoto, CategNome, dbo.fnSaldoEstoque(ProduEmpresa, ProduId, NULL) as Estoque
                    FROM Produto
                    JOIN Categoria on CategId = ProduCategoria
                    JOIN Situacao on SituaId = ProduStatus
                    WHERE ProduEmpresa = " . $_SESSION['EmpreId'] . " and SituaChave = 'ATIVO' 
                    ORDER BY ProduNome ASC ";
            $result = $conn->query("$sql");
            $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

            $cont = 1;
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    if ($cont == 1) {
        $cont = 0;
        foreach ($rowData as $item) {
            $cont++;

            if ($item['ProduFoto'] != null) {

                //Depois verifica se o arquivo físico ainda existe no servidor
                if (file_exists("global_assets/images/produtos/" . $item['ProduFoto'])) {
                    $sFoto = "global_assets/images/produtos/" . $item['ProduFoto'];
                } else {
                    $sFoto = "global_assets/images/lamparinas/sem_foto.gif";
                }
            } else {
                $sFoto = "global_assets/images/lamparinas/sem_foto.gif";
            }



            if ($item['Estoque'] > 0) {
                print('
                    <div class="col-xl-3 col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-img-actions">
                                    <a href="' . $sFoto . '" class="fancybox">
                                        <img src="' . $sFoto . '" class="card-img"  alt="" style="max-height:290px;">
                                        <span class="card-img-actions-overlay card-img">
                                            <i class="icon-plus3 icon-2x"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>

                            <div class="card-body bg-light text-center">
                                <div class="mb-2">
                                    <h6 class="font-weight-semibold mb-0">
                                        <a href="#" class="text-default">' . $item['ProduNome'] . '</a>
                                    </h6>

                                    <a href="#" class="text-muted">' . $item['CategNome'] . '</a>
                                </div>
                                <div class="text-muted mb-3">' . $item['Estoque'] . ' em estoque</div>

                                <button produId=' . $item['ProduId'] . ' type="button" class="btn btn-produtos bg-teal-400 add-cart"><i class="icon-cart-add mr-2"></i> Adicionar ao carrinho</button>
                            </div>
                        </div>
                    </div>							
                ');
            } else {
                print('
                    <div class="col-xl-3 col-sm-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-img-actions">
                                    <a href="' . $sFoto . '" class="fancybox">
                                        <img src="' . $sFoto . '" class="card-img"  alt="" style="max-height:290px;">
                                        <span class="card-img-actions-overlay card-img">
                                            <i class="icon-plus3 icon-2x"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>

                            <div class="card-body bg-light text-center">
                                <div class="mb-2">
                                    <h6 class="font-weight-semibold mb-0">
                                        <a href="#" class="text-default">' . $item['ProduNome'] . '</a>
                                    </h6>

                                    <a href="#" class="text-muted">' . $item['CategNome'] . '</a>
                                </div>
                                <div class="text-muted mb-3">' . $item['Estoque'] . ' em estoque</div>

                                <button produId=' . $item['ProduId'] . ' type="button" class="btn btn-produtos bg-teal-400 add-cart" disabled><i class="icon-cart-add mr-2"></i> Adicionar ao carrinho</button>
                            </div>
                        </div>
                    </div>							
                ');
            }
        }
    }
}

queryPesquisa();
