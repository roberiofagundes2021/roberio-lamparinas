<?php include_once("acesso.php"); ?>  

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Intranet | Oficiais e Comissões</title>
<!-- Essa meta abaixo que faz com que reconheça que está usando ou nao um mobile -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

<?php include_once("head.php"); ?>

</head>
<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">

  <?php include_once("topo.php"); ?>
    
  <?php include_once("menu.php"); ?>

  <div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <section class="content-header" style="margin-bottom:20px;">
      <h1>
      Intranet
      <small>Oficiais e Comissões</small>
      </h1>
      <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Oficiais e Comissões</li>
      </ol>
    </section>
    
    <section class="content">
	  <div class="row">
		<div class="col-xs-12">
		  <div class="box">
			<div class="box-header">
			  <h3 class="box-title">Oficiais e Comissões</h3>
			  <div class="box-tools">
				<div class="input-group" style="width: 150px; display:none;">
				  <input type="text" name="table_search" class="form-control input-sm pull-right" placeholder="Buscar">
				  <div class="input-group-btn">
					<button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
				  </div>
				</div>
			  </div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive no-padding">
				<table class="table table-hover">
			  
				  <div style="padding: 20px;">
				  <?php
				  
					$conexao = mysqli_connect("179.188.16.95","estreladebrasi6","anjo123","estreladebrasi6");
					//mysql_select_db("estreladebrasi6");
				  
					$query = "SELECT i.title, a.filename
						  FROM est_k2_items i
						  JOIN est_k2_categories c on c.id = i.catid
						  LEFT JOIN est_k2_attachments a on a.itemID = i.id
						  WHERE c.alias = 'oficiais-e-comissoes' and i.published=1 and i.trash=0
						  ORDER BY i.created DESC                  
						  ";
					$envia = mysqli_query($conexao, $query);
					
					$cont = 1;
					while ($resultado = mysqli_fetch_array($envia)){
					  echo "<div style='padding: 20px; float: left;'>";              
					  echo "<a href='../antigo/media/k2/attachments/" . $resultado['filename'] . "' target='_blank'><i class='fa fa-file-pdf-o'></i> <span>".utf8_encode($resultado['title'])."</span></a>";
					  echo "</div>";
					}
					   
				  ?>
				  </div>          
			  </table>
			  <p>&nbsp;</p>
			</div><!-- /.box-body -->
		  </div><!-- /.box -->
		</div>
	  </div>
    </section>
  </div> <!-- ./content-wrapper -->  
</div><!-- ./wrapper -->  
  
<?php include_once("scripts.php"); ?>

</body>
</html>
