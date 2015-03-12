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
	if(!empty($_POST)):
		$poste = $_POST;
		if(!empty($poste["tabela"])):
			$tabela = base64_decode($poste['tabela']);
			unset($poste["tabela"]);
			if(!empty($poste["where"])):
				$ordena = json_decode(base64_decode($poste['where']),true);
				unset($poste["where"]);			
				if(isset($poste["img_atual"])):
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
								$local = "../../uploads/".$tabela."/".$poste["foto"];
								move_uploaded_file($arquivo_tempor,$local); 
								$resultado['img_atual'] = $poste["foto"];
							endif;								
						else:
							$resultado['msg'] = "erro";
							exit();
						endif;
						if(file_exists("../../uploads/".$tabela."/".$poste["img_atual"]) && $poste["img_atual"] != "" ):
							unlink("../../uploads/".$tabela."/".$poste["img_atual"]);
						endif;
					endif;	
					unset($poste["img_atual"]);													
				endif;
				if(isset($poste["myDoc"])):
					unset($poste["myDoc"]);		
				endif;	
			$banco->atualiza($tabela,$poste,$ordena);
			$resultado['msg'] = "sucesso";	
			else:
				$resultado['msg'] = "erro";
			endif;

		endif;	
	endif;
	echo json_encode($resultado);
endif;	
?>