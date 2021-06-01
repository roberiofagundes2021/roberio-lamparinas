<?php
  include('global_assets/php/conexao.php');
  $unidade = $_SESSION['UnidadeId'];
  $perfil = $_SESSION['PerfiChave'];

  $sqlPerfil = "SELECT PerfiId FROM Perfil
  WHERE PerfiChave = '$perfil'";

  $resultPerfilId = $conn->query($sqlPerfil);
  $perfilId = $resultPerfilId->fetchAll(PDO::FETCH_ASSOC);
  $perfilId = $perfilId[0]['PerfiId'];

  //Recupera todos os módulos do sistema
  $sqlModulo = "SELECT ModulId, ModulOrdem, ModulNome, ModulStatus, SituaChave, SituaCor
                FROM Modulo 
                JOIN Situacao on ModulStatus = SituaId 
                ORDER BY ModulOrdem ASC";

  $resultModulo = $conn->query($sqlModulo);
  $modulo = $resultModulo->fetchAll(PDO::FETCH_ASSOC);

  //Recupera todos os menus do sistema
  $sqlMenu = "SELECT MenuId, MenuNome, MenuUrl, MenuIco, MenuSubMenu, MenuModulo, MenuSetorPublico,
              MenuPai, MenuLevel, MenuOrdem, MenuStatus, SituaChave, PrXPeId, PrXPePerfil, MenuSetorPrivado
              PrXPeMenu, PrXPeVisualizar, PrXPeAtualizar,  PrXPeExcluir, PrXPeUnidade
              FROM menu
              join situacao on MenuStatus = SituaId
              join PerfilXPermissao on MenuId = PrXPeMenu and PrXPePerfil = '$perfilId' and PrXPeUnidade  = '$unidade'
              order by MenuOrdem asc";

  $resultMenu = $conn->query($sqlMenu);
  $menu = $resultMenu->fetchAll(PDO::FETCH_ASSOC);

  //Recupera o parâmetro pra saber se a empresa é pública ou privada
  $sqlParametro = "SELECT ParamEmpresaPublica 
                   FROM Parametro
                   WHERE ParamEmpresa = ".$_SESSION['EmpreId'];
  $resultParametro = $conn->query($sqlParametro);
  $parametro = $resultParametro->fetch(PDO::FETCH_ASSOC);	
  
  $empresa = $parametro['ParamEmpresaPublica'] ? 'Publica' : 'Privada';
?>

<!-- Main sidebar -->
<div class="sidebar sidebar-dark sidebar-main sidebar-expand-md">

  <!-- Sidebar mobile toggler -->
  <div class="sidebar-mobile-toggler text-center">
    <a href="#" class="sidebar-mobile-main-toggle">
      <i class="icon-arrow-left8"></i>
    </a>
    <span class="font-weight-semibold">Navigation</span>
    <a href="#" class="sidebar-mobile-expand">
      <i class="icon-screen-full"></i>
      <i class="icon-screen-normal"></i>
    </a>
  </div>
  <!-- /sidebar mobile toggler -->


  <!-- Sidebar content -->
  <div class="sidebar-content">

    <!-- User menu -->
    <div class="sidebar-user-material">
      <div class="sidebar-user-material-body">
        <div class="card-body text-center">
          <a href="index.php">
            <!-- src="global_assets/images/placeholders/placeholder.jpg" class="rounded-circle shadow-1 -->
            <img src="global_assets/images/lamparinas/logo-lamparinas_200x200.jpg" class="img-fluid shadow-5 mb-3" width="100" height="100" alt="" style="padding-top:8px;visibility:hidden">
          </a>
          <h6 class="mb-0 text-white text-shadow-dark"><?php //echo nomeSobrenome($_SESSION['UsuarNome'],2); ?></h6>
          <span class="font-size-sm text-white text-shadow-dark"><?php //echo $_SESSION['UnidadeNome']; ?></span>
        </div>

        <div class="sidebar-user-material-footer" style="margin-top:40px;">
          <a href="#user-nav" class="d-flex justify-content-between align-items-center text-shadow-dark dropdown-toggle" data-toggle="collapse"><span>Minha Conta</span></a>
        </div>
      </div>

      <div class="collapse" id="user-nav">
        <ul class="nav nav-sidebar">
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="icon-user-plus"></i>
              <span>Meu Perfil</span>
            </a>
          </li>
          <!--<li class="nav-item">
								<a href="#" class="nav-link">
									<i class="icon-coins"></i>
									<span>Minha bandeja</span>
								</a>
							</li>-->
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="icon-comment-discussion"></i>
              <span>Minha bandeja</span>
              <span class="badge bg-teal-400 badge-pill align-self-center ml-auto">5</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="icon-cog5"></i>
              <span>Configurar Conta</span>
            </a>
          </li>
          <li class="nav-item">
            <a href="sair.php" class="nav-link">
              <i class="icon-switch2"></i>
              <span>Sair</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
    <!-- /user menu -->


    <!-- Main navigation -->
    <div class="card card-sidebar-mobile">
      <ul class="nav nav-sidebar" data-nav-type="accordion">
      <?php
          foreach($modulo as $mod){
            if($mod['SituaChave'] == strtoupper("ativo")){
              echo '<li class="nav-item-header">
                      <div class="text-uppercase font-size-xs line-height-xs">'.$mod['ModulNome'].'</div>
                    </li>';
              foreach($menu as $men){
                if ($men["MenuModulo"] == $mod["ModulId"] && $men["MenuPai"]==0 && $men['SituaChave'] == strtoupper("ativo")){  
                  
                  //Empresa pública e o menu visível para o Setor Público ou Empresa Privada e o menu visível para o Setor Privado
                  if (($empresa == 'Publica' && $men['MenuSetorPublico']) || ($empresa == 'Privada' && $men['MenuSetorPrivado'])  && $men['PrXPeVisualizar'] == 1){
                      echo  (($men['MenuSubMenu'] == 1) ? '<li class="nav-item nav-item-submenu">':'<li class="nav-item">').
                        '<a href="'.$men['MenuUrl'].'"';
                        if((basename($_SERVER['PHP_SELF']) == $men['MenuUrl']))
                          {echo 'class="nav-link active">';}else{echo 'class="nav-link">';}
                        echo '<i class="'.$men['MenuIco'].'"></i>
                        <span>'.
                          $men['MenuNome']
                        .'</span>
                      </a>';
                  }

                  if($men['MenuSubMenu'] == 1) {

                    echo '<ul class="nav nav-group-sub" data-submenu-title="Text editors">';

                    foreach($menu as $men_f){
                  
                      if($men_f['MenuPai'] == $men['MenuId'] && $men_f['PrXPeVisualizar'] == 1){
                          
                        if (($empresa == 'Publica' && $men_f['MenuSetorPublico']) || ($empresa == 'Privada' && $men_f['MenuSetorPrivado'])){
                          echo  '<li class="nav-item"><a href="'.$men_f['MenuUrl'].'" class="nav-link">'.$men_f['MenuNome'].'</a></li>';
                        }
                      } 
                    } 
                    
                    echo '</ul>';
                  }

                  echo '</li>';
                }
              }
            }
          }?>
      </ul>
    </div>
    <!-- /Main navigation -->
  </div>
</div>