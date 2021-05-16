<?php
  include('global_assets/php/conexao.php');

  $sqlModulo = "SELECT M.ModulId, M.ModulOrdem, M.ModulNome, M.ModulStatus, S.SituaChave, S.SituaCor
  FROM modulo M join situacao S on M.ModulStatus = S.SituaId order by M.ModulOrdem asc";
  $resultModulo = $conn->query($sqlModulo);
  $modulo = $resultModulo->fetchAll(PDO::FETCH_ASSOC);

  $sqlMenu = "SELECT M.MenuId, M.MenuNome, M.MenuUrl, M.MenuIco, M.MenuSubMenu, M.MenuModulo,
  M.MenuPai, M.MenuLevel, M.MenuOrdem, M.MenuStatus, S.SituaChave
  FROM menu M join situacao S on M.MenuStatus = S.SituaId
  order by MenuOrdem asc";
  $resultMenu = $conn->query($sqlMenu);
  $menu = $resultMenu->fetchAll(PDO::FETCH_ASSOC);

  $sqlSituacao = "SELECT SituaId, SituaNome, SituaChave, SituaStatus, SituaCor FROM situacao";
  $resultSituacao = $conn->query($sqlSituacao);
  $situacao = $resultSituacao->fetchAll(PDO::FETCH_ASSOC);		
		
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
                  echo  ($men['MenuSubMenu'] == 1? '<li class="nav-item nav-item-submenu">':'<li class="nav-item">').
                            '<a href="'.$men['MenuUrl'].'"';
                            if((basename($_SERVER['PHP_SELF']) == $men['MenuUrl']))
                              {echo 'class="nav-link active">';}else{echo 'class="nav-link">';}
                            echo '<i class="'.$men['MenuIco'].'"></i>
                            <span>'.
                              $men['MenuNome']
                            .'</span>
                          </a>';
                    if ($men['MenuSubMenu'] == 1){
                      echo '<ul class="nav nav-group-sub" data-submenu-title="Text editors">';
                      foreach($menu as $men_f){
                        if($men_f['MenuPai'] == $men['MenuId']){
                          echo  '<li class="nav-item"><a href="'.$men_f['MenuUrl'].'" class="nav-link">'.$men_f['MenuNome'].'</a></li>';
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