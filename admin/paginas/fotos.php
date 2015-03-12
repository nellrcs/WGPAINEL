<?php
	class fotos
	{
		/*
		* ver 1.0.0
		*/
		public static $tabela = "fotos"; 	
		public static $menu_nome = array('Fotos'=>'pagina=fotos');
		
		public static $largura = 1024;
		public static $altura = 768;
		public static $tamnaho_k_bytes = 700;

		public static function view()
		{
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
					'nome'=>1,
					'foto'=>2));

			return $tabelas;
		}

		public static function novo()
		{
			/*GERA HTML PAGINA NOVO */
			/* https://github.com/nellrcs/wgform */
			$form_foto = html_upload_foto(self::$largura,self::$altura,self::$tamnaho_k_bytes);

			$form = new WGform(); 
			$form::$nome = 'formulario';
			$form::$action = 'io/inserir.php';
			$form::$method = "POST";
			$form::$html_botao = $form_foto."<br><button type='submit' class='btn btn-primary'> SALVAR </button>";
			$form::$ajax = array('enviado_redireciona','erro','antes_enviar','completo');
			$paramtros_obj = array();

		
			/*CAMPOS*/	
			/*campo 'tabela' obrigatorio */
			$paramtros_obj['tabela'] = $form::index(array('tipo'=>5,'campo'=>'tabela','valor'=>base64_encode(self::$tabela)));
			
			/* capos relativos ao metodo cria_tebelas */
			$paramtros_obj['nome'] = $form::index(array('tipo'=>0,'campo'=>'nome','label'=>true,'html_depois'=>'<br>','titulo'=>'Nome','obrigatorio'=>true,"html_depois"=>"<hr>"));

			html_botao_add_refresh_lista(self::$tabela,adiciona_ao_get(array('op'=>"novo")));
			echo $form::formulario($paramtros_obj);
		}

		public static function editar($base)
		{
/*			$idioma = "";
			$pode_img = true;*/

			$id = addslashes($_GET['id']);
			$where = array('ID'=>$id); 
			$tabela = self::$tabela;
			$db_serv = $base->seleciona(self::$tabela,$where,null);
			$where = json_encode($where);
			$ln = $db_serv[0];

			$img_atual =  !empty($ln->foto) ? "../uploads/".self::$tabela."/".$ln->foto : "";

			$form = new WGform(); 
			$form::$nome = 'formulario';
			$form::$action = 'io/update.php';
			$form::$method = "POST";

			$form::$html_botao = "<br>".html_upload_foto(self::$largura,self::$altura,self::$tamnaho_k_bytes,$img_atual)."<br><button type='submit' class='btn btn-primary'> EDITAR </button>";

			$form::$ajax = array('enviado_redireciona','erro','antes_enviar','completo');
			$paramtros_obj = array();

			/*CAMPOS*/	
			/*campo 'tabela' obrigatorio */
			$paramtros_obj['tabela'] = $form::index(array('tipo'=>5,'campo'=>'tabela','valor'=>base64_encode($tabela)));
			$paramtros_obj['where'] = $form::index(array('tipo'=>5,'campo'=>'where','valor'=>base64_encode($where)));
			
			/* capos relativos ao metodo cria_tebelas */
			$paramtros_obj['nome'] = $form::index(array('tipo'=>0,'campo'=>'nome','label'=>true,'titulo'=>'Nome','valor'=>$ln->nome,"html_depois"=>"<hr>"));

			html_botao_add_refresh_lista(self::$tabela,adiciona_ao_get(array('op'=>"novo")));

			echo $form::formulario($paramtros_obj);
		}
		
		public static function listar($base)
		{ 
			$ln = $base->seleciona(self::$tabela,null,null);	
			html_botao_add_refresh(adiciona_ao_get(array('op'=>"novo")));
			if(empty($ln)):
				html_lista_vazia();	
			else:
				foreach($ln as $valor): 
					$camp_id = $valor->ID;	
					$d = array('id'=>$camp_id);
					$where_del = json_encode($d);
		  			$url_imagem = "thumb.php?tipo=red&img=../uploads/".self::$tabela."/".$valor->foto."&amp;w=160&amp;h=150";
					$onclick = "apagar('".base64_encode(self::$tabela)."','".base64_encode($where_del)."','')";
					self::html_item($valor,$url_imagem,$onclick,$camp_id);
				endforeach; 
		 	endif;

		}

		public static function html_item($obj,$url_imagem,$onclick,$camp_id)
		{?>
			<div class="col-lg-3">
			  	<div class="thumbnail">
			  		<img src="<?= $url_imagem; ?>" >
			  		<div>
						<p><strong><?= $obj->nome; ?></strong></p>
					</div>	
					<div>
						<a href="<?= adiciona_ao_get(array('op'=>"editar",'id'=>$camp_id)); ?>" class="btn btn-primary" role="button"><i class="glyphicon glyphicon-edit"></i></a>
						<a onclick="<?= $onclick; ?>" class="btn btn-danger" role="button"><i class="glyphicon glyphicon-trash"></i></a>
						<a href="#" class="btn btn-default disabledr" role="button"><i class="glyphicon glyphicon-cog"></i></a>
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
