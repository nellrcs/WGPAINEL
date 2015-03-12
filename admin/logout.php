<?php 
	include "../config.php";
	include "../s_lib/conexao.class.php";
	include "../s_lib/aes.class.php";
	include "../s_lib/aesctr.class.php";
	include "../s_lib/seguranca.class.php";
	include "../s_lib/funcoes_basicas.php";
	$permissao = new Seguranca();
	$permissao->logout();
	header("Location: login.php");

 ?>