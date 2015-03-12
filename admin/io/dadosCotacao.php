<?php 
include "../../config.php";
include "../../s_lib/conexao.class.php";
include "../../s_lib/aes.class.php";
include "../../s_lib/aesctr.class.php";
include "../../s_lib/seguranca.class.php";
include "../../s_lib/funcoes_basicas.php";
$banco = new Conexao();
$permissao = new Seguranca();
if($permissao->login() != true):
	header("Location: login.php");
	exit();	
endif;

$valores = $banco->seleciona('cotacao');

die(json_encode($valores) );
 ?>