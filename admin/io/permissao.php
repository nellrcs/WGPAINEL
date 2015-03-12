<?php
include "../../config.php";
include "../../s_lib/conexao.class.php";
include "../../s_lib/aes.class.php";
include "../../s_lib/aesctr.class.php";
include "../../s_lib/seguranca.class.php";
include "../../s_lib/funcoes_basicas.php";

$permissao = new Seguranca();
$json = array('url'=>'','status'=>false);
if(!empty($_POST['usuario'])):
	$obj = new stdClass();	
   	$obj->login   = addslashes($_POST['usuario']);
    $obj->senha  = addslashes($_POST['senha']);  
	$login = $permissao->login($obj);

	if($login != false):
	    $json['url'] = 'index.php';
	    $json['status'] = true;
	    echo json_encode($json);
	endif;
else:
	if($permissao->login() != true):
		header("Location: login.php");
	endif;
endif;

?>