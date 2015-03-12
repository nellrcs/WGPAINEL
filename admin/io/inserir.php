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
	echo "Acesso negado";
	exit();
else:
	$resultado =  array();
	$resultado['img_atual'] = "";
	if(!empty($_POST)):
		$poste = $_POST;	
		if(!empty($poste["tabela"])):
			$tabela = base64_decode($poste['tabela']);
			unset($poste["tabela"]);

			if(isset($poste["img_atual"])):
				unset($poste["img_atual"]);		
			endif;
			if(!empty($_FILES["foto"]["type"])):

				$ext_valida = array("jpeg", "jpg", "png");
				$temp = explode(".",$_FILES["foto"]["name"]);
				$extencao_arquivo = strtolower(end($temp));


				if ((($_FILES["foto"]["type"] == "image/png") || ($_FILES["foto"]["type"] == "image/jpg") || ($_FILES["foto"]["type"] == "image/jpeg") ) && ($_FILES["foto"]["size"] < 100000000) && in_array($extencao_arquivo, $ext_valida)): 
					
					if ($_FILES["foto"]["error"] > 0):
						$resultado['msg'] = "Codigo do erro:" . $_FILES["foto"]["error"];;
					else:
						$arquivo_tempor = $_FILES['foto']['tmp_name']; 
						$poste["foto"] = md5(uniqid(time())).".".$extencao_arquivo;
						if (!file_exists("../../uploads/".$tabela)) {
						    mkdir("../../uploads/".$tabela, 0777, true);
						}
						$local = "../../uploads/".$tabela."/".$poste["foto"];
						move_uploaded_file($arquivo_tempor,$local); 
						$resultado['img_atual'] = $poste["foto"];
					endif;								
				else:
					$resultado['msg'] = "erro";
					exit();
				endif;
			endif;
			$id = $banco->inserir($tabela,$poste);
			$resultado['id'] = $id;
		endif;	
	endif;
	echo json_encode($resultado);
endif;	
?>