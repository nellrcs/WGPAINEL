<?php 
$base = new Conexao(); 

$largura = 300;
$altura = 190;
$tamnaho_k_bytes = 700;  

if(!empty($_GET['lang']) ):
	$lang = addslashes($_GET['lang']);
endif;	

$up_where = "";
if(!empty($_GET['tipo'])):   
    $tipo = addslashes($_GET['tipo']);
	if($tipo):
		$where1 = array("tipo"=>$tipo,"sigla_idioma"=>"");
		$db_tex = $base->seleciona('textos',$where1,null);
		$up_where = json_encode($where1);
		if($db_tex):
			if(!empty($lang )):
				$where2 = array("tipo"=>$tipo,"sigla_idioma"=>$lang);
				$db_tex = $base->seleciona('textos',$where2,null);
				$up_where = json_encode($where2);

				if($db_tex):
					$db_tex = $db_tex;
				else:
					$db_idiom = $base->seleciona('idiomas',array('sigla'=>$lang,"status"=>1),null);
					if($db_idiom):
						$base->inserir('textos',array('sigla_idioma'=>$lang,"tipo"=>$tipo));
						$db_tex = $base->seleciona('textos',$where2,null);
						$up_where = json_encode($where2);
					else:
						$db_tex = $base->seleciona('textos',$where1,null);
						$up_where = json_encode($where1);	
					endif;		
				endif;
			endif;
			$ln = $db_tex[0];
		endif;	
	endif;	
else:
    $tipo = false;
endif;

if (!empty($ln)): 

	html_lista_idiomas();
	html_topo_pagina('Texto:'.ucfirst($ln->tipo));	

	$form = new WGform(); 

	$img_atual =  !empty($ln->foto) ? "../uploads/textos/".$ln->foto : "";

	$form::$nome = 'formulario';
	$form::$action = 'io/update.php';
	$form::$method = "POST";

	if($img_atual):
		$form::$html_botao = html_upload_foto($largura,$altura,$tamnaho_k_bytes,$img_atual)."<br><button type='submit' class='btn btn-primary'> SALVAR </button>";
	else:
		$form::$html_botao = "<br><button type='submit' class='btn btn-primary'> SALVAR </button>";
	endif;

	$form::$ajax = array('enviado_redireciona','erro','antes_enviar','completo');
	$paramtros_obj = array();

	/*CAMPOS*/	
	/*campo 'tabela' obrigatorio */
	$paramtros_obj['tabela'] = $form::index(array('tipo'=>5,'campo'=>'tabela','valor'=>base64_encode('textos')));
	$paramtros_obj['where'] = $form::index(array('tipo'=>5,'campo'=>'where','valor'=>base64_encode($up_where)));
	
	/* capos relativos ao metodo cria_tebelas */
	$paramtros_obj['titulo'] = $form::index(array('tipo'=>0,'campo'=>'titulo','label'=>true,'titulo'=>'Titulo','html_depois'=>'<br>','valor'=>$ln->titulo));
	$paramtros_obj['texto'] = $form::index(array('tipo'=>1,'campo'=>'texto','label'=>true,'titulo'=>'Descricao','css_class'=>'form-control','id'=>'editor_area','valor'=>$ln->texto));

	echo $form::formulario($paramtros_obj);

else: 
  if(NIVEL_USUARIO == 5):	
  	echo "NOVO TEXTO";
  else:
  	include("erro.php");
  endif;	
endif; 
?>	
