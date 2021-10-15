<?php

include_once("sessao.php");
include('global_assets/php/conexao.php');

function queryPesquisa()
{

    $cont = 0;

    include('global_assets/php/conexao.php');

    $args = [];

    if (!empty($_POST['inputPesquisaProduto'])) {
        $args[]  = $_POST['inputProdutoServico'] == 'P'? "ProduNome LIKE '%" . $_POST['inputPesquisaProduto'] . "%'" : "ServiNome LIKE '%" . $_POST['inputPesquisaProduto'] . "%'";
    }

    if (!empty($_POST['inputCategoria'])) {
        $args[]  = $_POST['inputProdutoServico'] == 'P'? "ProduCategoria = " . $_POST['inputCategoria'] . " " : "ServiCategoria = " . $_POST['inputCategoria'] . " ";
    }

    if (!empty($_POST['inputSubCategoria'])) {
        $args[]  = $_POST['inputProdutoServico'] == 'P'? "ProduSubCategoria = " . $_POST['inputSubCategoria'] . " " : "ServiSubCategoria = " . $_POST['inputSubCategoria'] . " ";
    }

    if (!empty($_POST['inputMarca'])) {
        $args[]  = $_POST['inputProdutoServico'] == 'P'? "ProduMarca = " . $_POST['inputMarca'] . " " : "ServiMarca = " . $_POST['inputMarca'] . " ";
    }

    if (!empty($_POST['inputFabricante'])) {
        $args[]  = $_POST['inputProdutoServico'] == 'P'? "ProduFabricante = " . $_POST['inputFabricante'] . " " : "ServiFabricante = " . $_POST['inputFabricante'] . " ";
    }

    if (!empty($_POST['inputModelo'])) {
        $args[]  = $_POST['inputProdutoServico'] == 'P'? "ProduModelo = " . $_POST['inputModelo'] . " " : "ServiModelo = " . $_POST['inputModelo'] . " ";
    }


    if (count($args) >= 1) {
        try {

            $string = implode(" and ", $args);

            if ($string != '') {
                $string .= ' and ';
            }

            if ($_POST['inputProdutoServico'] == 'S'){
                $sql = "SELECT ServiId as Id, ServiCodigo as Codigo, ServiNome as Nome, ServiDetalhamento as Detalhamento, 
                CategNome, dbo.fnSaldoEstoque(ServiUnidade, ServiId, 'S', NULL) as Estoque
                FROM Servico
                JOIN Categoria on CategId = ServiCategoria
                JOIN Situacao on SituaId = ServiStatus
                WHERE " . $string . " ServiUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO' ";
            } else {
                $sql = "SELECT ProduId as Id, ProduCodigo as Codigo, ProduNome as Nome, ProduDetalhamento as Detalhamento, 
                ProduFoto, CategNome, dbo.fnSaldoEstoque(ProduUnidade, ProduId, 'P', NULL) as Estoque
                FROM Produto
                JOIN Categoria on CategId = ProduCategoria
                JOIN Situacao on SituaId = ProduStatus
                WHERE " . $string . " ProduUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO' ";
            }
            // var_dump($sql);
            // exit;

            $result = $conn->query($sql);
            $rowData = $result->fetchAll(PDO::FETCH_ASSOC);

            count($rowData) >= 1 ? $cont = 1 : $cont = 0;
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    } else {
        try {
            if ($_POST['inputProdutoServico'] == 'S'){
                $sql = "SELECT ServiId as Id, ServiCodigo as Codigo, ServiNome as Nome, ServiDetalhamento as Detalhamento, 
                CategNome, dbo.fnSaldoEstoque(ServiUnidade, ServiId, 'S', NULL) as Estoque
                FROM Servico
                JOIN Categoria on CategId = ServiCategoria
                JOIN Situacao on SituaId = ServiStatus
                WHERE ServiUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO' 
                ORDER BY ServiNome ASC ";
            } else {
                $sql = "SELECT ProduId as Id, ProduCodigo as Codigo, ProduNome as Nome, ProduDetalhamento as Detalhamento, 
                ProduFoto, CategNome, dbo.fnSaldoEstoque(ProduUnidade, ProduId, 'P', NULL) as Estoque
                FROM Produto
                JOIN Categoria on CategId = ProduCategoria
                JOIN Situacao on SituaId = ProduStatus
                WHERE ProduUnidade = " . $_SESSION['UnidadeId'] . " and SituaChave = 'ATIVO'
                ORDER BY ProduNome ASC ";
            }
            $result = $conn->query($sql);
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

            $sFoto = "global_assets/images/lamparinas/sem_foto.gif";

            if ($_POST['inputProdutoServico'] == 'P'){               

                if ($item['ProduFoto'] != null) {

                    //Depois verifica se o arquivo físico ainda existe no servidor
                    if (file_exists("global_assets/images/produtos/" . $item['ProduFoto'])) {
                        $sFoto = "global_assets/images/produtos/" . $item['ProduFoto'];
                    } else {
                        $sFoto = "global_assets/images/lamparinas/sem_foto.gif";
                    }
                }        
            }

            if ($item['Estoque'] > 0) {
                print('
                    <div class="col-xl-3 col-sm-3">
                        <div class="card">');
                
                if ($_POST['inputProdutoServico'] == 'P'){
                    print('        
                    <div class="card-body">
                        <div class="card-img-actions" id="Imagens">
                            <a href="' . $sFoto . '" class="fancybox">
                                <img src="' . $sFoto . '" class="card-img"  alt="" style="max-height:250px;">
                                <span class="card-img-actions-overlay card-img">
                                    <i class="icon-plus3 icon-2x"></i>
                                </span>
                            </a>
                        </div>
                    </div>');
                }

                print('
                            <div class="card-body bg-light text-center">
                                <div class="mb-2">
                                    <h6 class="font-weight-semibold mb-0" data-popup="tooltip" title="' . $item['Detalhamento'] . '" style="height: 46.1667px; overflow: hidden">
                                        <a href="#" class="text-default">' . $item['Nome'] . '</a>
                                    </h6>

                                    <a href="#" class="text-muted">' . $item['CategNome'] . '</a>
                                </div>
                                <div class="text-muted mb-3">' . $item['Estoque'] . ' em estoque</div>

                                <button produId=' . $item['Id'] . ' type="button" class="btn btn-produtos bg-teal-400 add-cart"><i class="icon-cart-add mr-2"></i> Adicionar ao carrinho</button>
                            </div>
                        </div>
                    </div>							
                ');
            } else {
                print('
                    <div class="col-xl-3 col-sm-3">
                        <div class="card">');
                
                if ($_POST['inputProdutoServico'] == 'P'){
                    print('                        
                    <div class="card-body">
                        <div class="card-img-actions" id="Imagens">
                            <a href="' . $sFoto . '" class="fancybox">
                                <img src="' . $sFoto . '" class="card-img"  alt="" style="max-height:250px;">
                                <span class="card-img-actions-overlay card-img">
                                    <i class="icon-plus3 icon-2x"></i>
                                </span>
                            </a>
                        </div>
                    </div>');
                }

                print('
                            <div class="card-body bg-light text-center">
                                <div class="mb-2">
                                    <h6 class="font-weight-semibold mb-0" data-popup="tooltip" title="' . $item['Detalhamento'] . '" style="height: 46.1667px; overflow: hidden">
                                        <a href="#" class="text-default">' . $item['Nome'] . '</a>
                                    </h6>

                                    <a href="#" class="text-muted">' . $item['CategNome'] . '</a>
                                </div>
                                <div class="text-muted mb-3">' . $item['Estoque'] . ' em estoque</div>

                                <button produId=' . $item['Id'] . ' type="button" class="btn btn-produtos bg-teal-400 add-cart" disabled><i class="icon-cart-add mr-2"></i> Adicionar ao carrinho</button>
                            </div>
                        </div>
                    </div>							
                ');
            }
        }
    }
}

queryPesquisa();
