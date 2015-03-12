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
		$where = json_decode(base64_decode($poste['where']),true);
		$tabela =base64_decode($poste['tabela']);
		$ob = $banco->seleciona($tabela,$where);
		if(!empty($ob)):
			$dados = $ob[0];	
			if(!empty($dados->foto)):
				if(file_exists("../../uploads/".$tabela."/".$dados->foto)):
					unlink("../../uploads/".$tabela."/".$dados->foto);
				endif;
			endif;			
			$banco->apaga($tabela,$where);
			if($poste['tambem'] != ""):
				$tambem = json_decode(base64_decode($poste['tambem']),true);


				if(!empty($tambem['tabela']) && !empty($tambem['where'])):
					$tambem_tabela = $tambem['tabela'];
					$tambem_where = $tambem['where'];
					$banco->apaga($tambem_tabela,$tambem_where);
				endif;	
			endif;

		$resultado["msg"] = "sucesso";	
		endif;	
	endif;
	echo json_encode($resultado);
endif;	
?>