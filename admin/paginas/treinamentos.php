<?php
	class treinamentos
	{
		/*
		* ver 1.0.0
		*/
		public static $tabela = "treinamentos"; 	
		public static $menu_nome = array('Treinamentos'=>'pagina=treinamentos');
		
		public static $largura = 1024;
		public static $altura = 768;
		public static $tamnaho_k_bytes = 700;

		public static function view()
		{
			html_lista_idiomas();
			html_topo_pagina(ucfirst(self::$tabela));
			$metodo = get_op_modo();
			$conexao = new Conexao(); 
			$nova_t = empty($_GET['criar_tabela']) ? false : true;
			$verifica_tabela = $conexao->verifica_tabela(self::$tabela,self::cria_tebelas(),$nova_t); 
			
			if($verifica_tabela == false):
				echo "<h3>A tabela ".self::$tabela." não existe, deseja criar uma nova tabela</h3>";
				echo "<a href='".adiciona_ao_get(array('criar_tabela'=>1))."' class='btn btn-default'><i class='glyphicon glyphicon-upload'></i> CRIAR TABELA</a>";
			else:
				self::$metodo($conexao);
				self::scripts();
			endif;		
		}

		public static function cria_tebelas()
		{	
			/* CRIAR TEBELA NO BANACO */
			$tabelas = array();
			$tabelas[] = array(
				self::$tabela=>
				array(
					'nome_treinameto'=>1,
					'descricao'=>2,
					'foto'=>2));

			$tabelas[] = array(
				self::$tabela."_traducao"=>
				array(
					"id_".self::$tabela=>0,
					'nome_treinameto'=>1,
					'descricao'=>2,
					'sigla_idioma'=>1));

			return $tabelas;
		}

		public static function novo()
		{
			/*GERA HTML PAGINA NOVO */
			/* https://github.com/nellrcs/wgform */

			$form = new WGform(); 
			$form::$nome = 'formulario';
			$form::$action = 'io/inserir.php';
			$form::$method = "POST";
			$form::$html_botao = html_upload_foto(self::$largura,self::$altura,self::$tamnaho_k_bytes)."<br><button type='submit' class='btn btn-primary'> SALVAR </button>";
			$form::$ajax = array('enviado_redireciona','erro','antes_enviar','completo');
			$paramtros_obj = array();

			/*CAMPOS*/	
			/*campo 'tabela' obrigatorio */
			$paramtros_obj['tabela'] = $form::index(array('tipo'=>5,'campo'=>'tabela','valor'=>base64_encode(self::$tabela)));
			
			/* capos relativos ao metodo cria_tebelas */
			$paramtros_obj['nome_treinameto'] = $form::index(array('tipo'=>0,'campo'=>'nome_treinameto','label'=>true,'titulo'=>'Nome do Treinamento','html_depois'=>'<br>'));
			$paramtros_obj['descricao'] = $form::index(array('tipo'=>1,'campo'=>'descricao','label'=>true,'titulo'=>'Descricao','css_class'=>'form-control','id'=>'editor_area'));

			html_botao_add_refresh_lista(self::$tabela,adiciona_ao_get(array('op'=>"novo")));
			echo $form::formulario($paramtros_obj);
		}

		public static function editar($base)
		{
			$idioma = "";
			$pode_img = true;

			if(!empty($_GET['lang'])):
				$pode_img = false; 
				$id = addslashes($_GET['id']);
				$lang =  addslashes($_GET['lang']);
				$tabela = self::$tabela."_traducao";
				$where = array('sigla_idioma' => $lang,'id_'.self::$tabela=>$id); 
				$db_serv = $base->seleciona($tabela,$where,null);

				if($db_serv):
					$ln = $db_serv[0];
				else:
					$db_idiom = $base->seleciona('idiomas',array('sigla'=>$lang,'status'=>1),null);
					if($db_idiom):
						$base->inserir($tabela,$where);
						$db_serv = $base->seleciona($tabela,$where,null);
					else:
						$db_serv = $base->seleciona($tabela,$where,null);
					endif;	
					$ln = $db_serv[0];	
				endif;	
				$where = json_encode($where);
		 	else: 
				$id = addslashes($_GET['id']);
				$where = array('ID'=>$id); 
				$tabela = self::$tabela;
				$db_serv = $base->seleciona(self::$tabela,$where,null);
				$where = json_encode($where);
				$ln = $db_serv[0];
			endif; 

			$img_atual =  !empty($ln->foto) ? "../uploads/".self::$tabela."/".$ln->foto : "";

			$form = new WGform(); 
			$form::$nome = 'formulario';
			$form::$action = 'io/update.php';
			$form::$method = "POST";
			
			if($pode_img):
				$form::$html_botao = html_upload_foto(self::$largura,self::$altura,self::$tamnaho_k_bytes,$img_atual)."<br><button type='submit' class='btn btn-primary'> SALVAR </button>";
			else:
				$form::$html_botao = "<br><button type='submit' class='btn btn-primary'> SALVAR </button>";
			endif;

			$form::$ajax = array('enviado_redireciona','erro','antes_enviar','completo');
			$paramtros_obj = array();

			/*CAMPOS*/	
			/*campo 'tabela' obrigatorio */
			$paramtros_obj['tabela'] = $form::index(array('tipo'=>5,'campo'=>'tabela','valor'=>base64_encode($tabela)));
			$paramtros_obj['where'] = $form::index(array('tipo'=>5,'campo'=>'where','valor'=>base64_encode($where)));
			
			/* capos relativos ao metodo cria_tebelas */
			$paramtros_obj['nome_treinameto'] = $form::index(array('tipo'=>0,'campo'=>'nome_treinameto','label'=>true,'titulo'=>'Nome do Treinamento','html_depois'=>'<br>','valor'=>$ln->nome_treinameto));
			$paramtros_obj['descricao'] = $form::index(array('tipo'=>1,'campo'=>'descricao','label'=>true,'titulo'=>'Descricao','css_class'=>'form-control','id'=>'editor_area','valor'=>$ln->descricao));

			html_botao_add_refresh_lista(self::$tabela,adiciona_ao_get(array('op'=>"novo")));

			echo $form::formulario($paramtros_obj);
		}
		
		public static function listar($base)
		{ 
			$idioma = "";
			if(!empty($_GET['lang'])): 
				$idioma =  addslashes($_GET['lang']); 	
				$ln = $base->seleciona(self::$tabela."_traducao",array('sigla_idioma' => $idioma),null);
		 	else: 
				$ln = $base->seleciona(self::$tabela,null,null);	
			endif; 
			html_botao_add_refresh(adiciona_ao_get(array('op'=>"novo")));
			if(empty($ln)):
				html_lista_vazia();	
			else:
				foreach($ln as $valor): 
					$camp_id = $valor->ID;	
					$d = array('id'=>$camp_id);
					$where_del = json_encode($d);
					$tambem = array("tabela"=>self::$tabela."_traducao","where" => array('id_'.self::$tabela =>$camp_id ));
					$tembem = json_encode($tambem);
					if(!empty($idioma)):
			  			$original = $base->seleciona(self::$tabela,array('ID'=>$valor->{'id_'.self::$tabela}),array("foto","ID")); 
			  			$foto = (isset($original[0]->foto) ? $original[0]->foto : "");
			  			$camp_id = (isset($original[0]->ID) ? $original[0]->ID : "");
			  			$url_imagem = "thumb.php?tipo=red&img=../uploads/".self::$tabela."/".$foto."&amp;w=160&amp;h=150";	  		
			  			$onclick = "apagar('".base64_encode(self::$tabela."_traducao")."','".base64_encode($where_del)."','')";
			  		else:
			  			$url_imagem = "thumb.php?tipo=red&img=../uploads/".self::$tabela."/".$valor->foto."&amp;w=160&amp;h=150";
						$onclick = "apagar('".base64_encode(self::$tabela)."','".base64_encode($where_del)."','".base64_encode($tembem)."')";
					endif;
					self::html_item($valor,$url_imagem,$onclick,$camp_id);
				endforeach; 
		 endif;

		}

		public static function html_item($obj,$url_imagem,$onclick,$camp_id)
		{?>
			<div class="panel panel-default">
			    <div class="panel-body">
					<div class="col-lg-3">
					  	<div class="thumbnail">
					  		<img src="<?= $url_imagem; ?>" >			  			
						</div>
					</div>
					<div class="col-lg-8">
						<h3><?= $obj->nome_treinameto; ?></h3>
						<p><?= $obj->descricao; ?></p>
					</div>

					<div class="col-lg-1">
						<p><a href="<?= adiciona_ao_get(array('op'=>"editar",'id'=>$camp_id)); ?>" class="btn btn-primary" role="button"><i class="glyphicon glyphicon-edit"></i></a></p>
						<p><a  onclick="<?= $onclick; ?>" class="btn btn-danger" role="button"><i class="glyphicon glyphicon-trash"></i></a></p>
						<p><a href="#" class="btn btn-default disabledr" role="button"><i class="glyphicon glyphicon-cog"></i></a></p>
					</div>
			    </div>
			</div>
		<?php
		}

		public static function scripts()
		{
		?>
			<script>
			function enviado_redireciona(data)
			{
				alert('eviado');
				var res = jQuery.parseJSON(data);
				if(res.img_atual)
				{
				  $('#img_atual').attr('value', res.img_atual);
				}	
				if(res.id)
				{
					window.location = '<?= adiciona_ao_get(array("op"=>"editar")); ?>&id='+res.id;
				}	
			}
			</script>
		<?php
		}

	} 

?>
