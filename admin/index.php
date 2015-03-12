<?php 
  include "../config.php";
	include "../s_lib/conexao.class.php";
  include "../s_lib/aes.class.php";
  include "../s_lib/aesctr.class.php";
	include "../s_lib/seguranca.class.php";
  include "../s_lib/funcoes_basicas.php";
	include "../s_lib/wgform.class.php";

	$permissao = new Seguranca(); 
  $ac = $permissao->login();
  if($ac != true):
		header("Location: login.php");
    exit();
  else:
    $dados = $permissao->decodifica($ac);
    $u = json_decode($dados);
    define('NOME_USUARIO', $u->n);  
    define('NIVEL_USUARIO', $u->a);  
  endif;

  $dir_pag = 'paginas/';
  $p = (addslashes(isset($_GET["pagina"])) ? $_GET["pagina"] : "erro" );
  $sidebar = true;

  if($p == 'cotacao'):
    $sidebar = false;
  endif;

  $nomes_menu = array();
  $filename = dirname(__FILE__).DIRECTORY_SEPARATOR.'paginas/';
  chdir($filename); 
  foreach(glob("*.php",GLOB_BRACE) as $arquivo) 
  {
    if(is_readable($arquivo)) 
    {
      require_once $arquivo;
      $nome_classe = basename($arquivo, '.php');
      if(!empty($nome_classe::$menu_nome)):
        $nomes_menu[] = $nome_classe::$menu_nome;
      endif;
    }
  }
  chdir(dirname(__FILE__).DIRECTORY_SEPARATOR);
 ?>
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">
    <title>Painel adminstrativo</title>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.mask.min.js"></script>
    <script src="js/main.js"></script>

    <link rel="stylesheet" href="dist/bootstrap-table.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script src="dist/bootstrap-table.min.js"></script>


    <script src="dist/extensions/export/tableExport.js"></script>
    <script src="dist/extensions/export/jquery.base64.js"></script>
    <script src="dist/extensions/export/bootstrap-table-export.min.js"></script>

    <script src="js/bootstrap-tagsinput.min.js"></script>
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="js/ie-emulation-modes-warning.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <nav class="navbar navbar-default">
      <div class="container">
        
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><img src="img/login-logo.png" alt="" width="140"></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown">
              <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">Configuracoes<b class="caret"></b></a>
              <ul class="dropdown-menu"> 
                <li><a href="?pagina=sistema&alt=email"><i class="glyphicon glyphicon-envelope"></i>&nbsp;&nbsp;Email</a></li> 
                <li><a href="?pagina=sistema&alt=endereco"><i class="glyphicon glyphicon-phone-alt"></i>&nbsp;&nbsp;Endereco/Contato</a></li> 
                <li><a href="?pagina=sistema&alt=seo"><i class="glyphicon glyphicon-search"></i>&nbsp;&nbsp;SEO</a></li> 
                <li><a href="?pagina=sistema&alt=redes_sociais"><i class="glyphicon glyphicon-thumbs-up"></i>&nbsp;&nbsp;Redes socias</a></li> 
                <li><a href="?pagina=sistema&alt=idiomas"><i class="glyphicon glyphicon-globe"></i>&nbsp;&nbsp;Idiomas</a></li> 
                <li><a href="?pagina=sistema&alt=palavras"><i class="glyphicon glyphicon-pencil"></i>&nbsp;&nbsp;Palavras</a></li>
                <li class="divider"></li> 
                <li><a href="?pagina=sistema&alt=usuarios"><i class="glyphicon glyphicon-user"></i>&nbsp;&nbsp;Usuarios</a></li> 
              </ul>
            </li>
            <?php  lista_textos_menu(); ?>
            <?php lista_paginas_menu($nomes_menu,5); ?>
          </ul>
          <ul class="nav navbar-nav navbar-right">  
            <li class="dropdown">
              <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><span class="label <?php if(NIVEL_USUARIO == 5): echo 'label-danger'; else: echo 'label-info'; endif;?>"><i class="glyphicon glyphicon-user"></i><?= NOME_USUARIO; ?><b class="caret"></b></span></a>
              <ul class="dropdown-menu">
                <li><a href="logout.php"><i class="glyphicon glyphicon-off"></i>sair</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
	
    <div class="container conteudo">

      <div class="row row-offcanvas row-offcanvas-right">

        <div class="col-xs-12 <?php if($sidebar == true ){ echo 'col-sm-9'; }else{ echo 'col-sm-12';} ?>">
           <?php
            switch($p) 
            {
              case 'sistema':
                include("sistema/index.php");
              break;
              default:
                if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.$dir_pag.$p.".php")): 
                  $p::view();
                else:
                  include('sistema/'."home.php");
                endif;
              break; 
            }
?> 

</div>
<?php if($sidebar == true): ?>
 <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar">
   <ul class="nav nav-pills nav-stacked">
  <!--       <li class="active"><a href="#" >Links Rapidos</a></li>
  <li><a href="http://www.whatismyip.com/" target="new">Meu ip</a></li>
  <li><a href="http://www.google.com/analytics/" target="new">Google analytics</a></li>
  <li><a href="https://translate.google.com.br" target="new">Google Tradutor</a></li>
  <li><a href="http://validator.w3.org/" target="new">W3c</a></li>
  <li><a href="#">Link</a></li>
  <li><a href="#">Link</a></li>
  <li><a href="#">Link</a></li>
  <li><a href="#">Link</a></li> -->
    </ul>

  </div>
<?php endif; ?>
        </div><!--/.sidebar-offcanvas-->
      </div><!--/row-->

      <hr>

      <footer>
      <div class="container">
        <p>&copy; Webinga 2015</p>
      </div>  
      </footer>

    </div><!--/.container-->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>

  </body>
</html>