<?php
	class videos
	{
		/*
		* ver 1.0.0
		*/
		public static $tabela = "videos"; 	
		public static $menu_nome = array('Videos'=>'pagina=videos');

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
					'url'=>1,
					'tipo'=>1));

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
			$form::$html_botao = "<br><button type='submit' class='btn btn-primary botao' disabled> SALVAR </button>";
			$form::$ajax = array('enviado_redireciona','erro','antes_enviar','completo');
			$paramtros_obj = array();

			/*CAMPOS*/	
			/*campo 'tabela' obrigatorio */
			$paramtros_obj['tabela'] = $form::index(array('tipo'=>5,'campo'=>'tabela','valor'=>base64_encode(self::$tabela)));
			
			/* capos relativos ao metodo cria_tebelas */
			$paramtros_obj['tipo'] = $form::index(array('tipo'=>5,'campo'=>'tipo','css_class'=>'tipo','valor'=>""));
			$paramtros_obj['url'] = $form::index(array('tipo'=>0,'campo'=>'url','css_class'=>'form-control url','label'=>true,'html_depois'=>'<br>','titulo'=>'Nome','obrigatorio'=>true,"html_depois"=>"<hr><br><div class='embed-responsive embed-responsive-16by9'><iframe class='embed-responsive-item' src='' allowfullscreen=''></iframe></div>"));
			

			html_botao_add_refresh_lista(self::$tabela,adiciona_ao_get(array('op'=>"novo")));
			echo $form::formulario($paramtros_obj);
		}

		public static function editar($base)
		{

			$id = addslashes($_GET['id']);
			$where = array('ID'=>$id); 
			$tabela = self::$tabela;
			$db_serv = $base->seleciona(self::$tabela,$where,null);
			$where = json_encode($where);
			$ln = $db_serv[0];

			$form = new WGform(); 
			$form::$nome = 'formulario';
			$form::$action = 'io/update.php';
			$form::$method = "POST";

			$form::$html_botao = "<br><button type='submit' class='btn btn-primary botao' disabled> EDITAR </button>";

			$form::$ajax = array('enviado_redireciona','erro','antes_enviar','completo');
			$paramtros_obj = array();

			/*CAMPOS*/	
			/*campo 'tabela' obrigatorio */
			$paramtros_obj['tabela'] = $form::index(array('tipo'=>5,'campo'=>'tabela','valor'=>base64_encode($tabela)));
			$paramtros_obj['where'] = $form::index(array('tipo'=>5,'campo'=>'where','valor'=>base64_encode($where)));
			
			/* capos relativos ao metodo cria_tebelas */
			$paramtros_obj['tipo'] = $form::index(array('tipo'=>5,'campo'=>'tipo','css_class'=>'tipo','valor'=>""));
			$paramtros_obj['url'] = $form::index(array('tipo'=>0,'campo'=>'url','label'=>true,'titulo'=>'Nome','css_class'=>'form-control url','valor'=>$ln->url,"html_depois"=>"<hr><br><div class='embed-responsive embed-responsive-16by9'><iframe class='embed-responsive-item' src='".$ln->url."' allowfullscreen=''></iframe></div>"));

			html_botao_add_refresh_lista(self::$tabela,adiciona_ao_get(array('op'=>"novo")));

			echo $form::formulario($paramtros_obj);
		}
		
		public static function listar($base)
		{ 
			$ln = $base->seleciona(self::$tabela,null,null,array('DESC'=>'ID'));	
			html_botao_add_refresh(adiciona_ao_get(array('op'=>"novo")));
			if(empty($ln)):
				html_lista_vazia(adiciona_ao_get(array('op'=>"novo")));	
			else:
				foreach($ln as $valor): 
					$camp_id = $valor->ID;	
					$d = array('id'=>$camp_id);
					$where_del = json_encode($d);
		  			$onclick = "apagar('".base64_encode(self::$tabela)."','".base64_encode($where_del)."','')";
					self::html_item($valor,null,$onclick,$camp_id);
				endforeach; 
		 	endif;

		}

		public static function html_item($obj,$url_imagem,$onclick,$camp_id)
		{?>
			<div class="panel panel-default">
			    <div class="panel-body">
					<div class="col-lg-4">
					  	<div class="thumbnail">
					  		<br><div class='embed-responsive embed-responsive-16by9'><iframe class='embed-responsive-item' src='<?= $obj->url; ?>' allowfullscreen=''></iframe></div>			  			
						</div>
					</div>
					<div class="col-lg-7">
						<img src="img/<?= $obj->tipo; ?>.png" width="30%" alt="<?= $obj->tipo; ?>">
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

				var regYoutube = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
				var regVimeo = /^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/;
				var tipo_video = "";

				function check_url(url) 
				{
				  if(regYoutube.test(url)) 
				  {
				    tipo_video = 'youtube';
				    return youtube_id(url);
				  }
				  else if(regVimeo.test(url)) 
				  {
				    tipo_video = 'vimeo';
				    return viemo_id(url)
				  }
				  else
				  {
				    return false;
				  }
				}

				function youtube_id(url){
				  var id = '';
				  url = url.replace(/(>|<)/gi,'').split(/(vi\/|v=|\/v\/|youtu\.be\/|\/embed\/)/);
				  if(url[2] !== undefined)
				  {
				    //id = url[2].split(/[^0-9a-z_]/i);
				    var link = "https://www.youtube.com/embed/"+url[2];
				    return link;
				  }
				  else 
				  {
				    return false;
				  }
				}

				function viemo_id(url)
				{
				  var regExp = /^.*(vimeo\.com\/)?vimeo.com\/(\d+)($|\/)/;
				  var match = url.match(regExp);
				  if (match)
				  {
				    var link = "//player.vimeo.com/video/"+match[2];
				    return link;
				  }
				  else
				  {
				    return false;
				  }
				}


				function enviado_redireciona(data)
				{
					alert('eviado');	
				}


				var urlField = $('#url');
				var vTipo = $('#tipo');
				var botao = $('.botao');	
				urlField.bind('paste keyup', function(e) {
				    setTimeout(function() {
				      vembed = check_url(urlField.val());
				      if(vembed != false)
				      {
				      	$('iframe').attr('src',vembed);
				        urlField.val(vembed);
				        vTipo.val(tipo_video);
				        botao.prop('disabled', false);
				      }	
				      else
				      {
				      	botao.prop('disabled', true);
				      }	
				    }, 0);
				});

				</script>
		<?php
		}

	} 

?>
