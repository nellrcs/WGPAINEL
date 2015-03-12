<?php
	class produtos
	{
		public static $tabela = "produtos";
		public static $tabela_categorias = "categorias";
		public static $menu_nome = 
		array(
			'Produtos'=>array(
			'Categorias'=>'produtos&lista=categorias',
			'Produtos'=>'produtos'
			));
		
		public static $largura = 800;
		public static $altura = 600;
		public static $tamnaho_k_bytes = 300;

		public static function view()
		{
			
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

		public static function novo($base)
		{
			if(!empty($_GET['novo'])):	
				$opcao = addslashes($_GET['novo']);
				switch ($opcao) 
				{
					case 'produto':
						return self::novo_produto($base);
					break;

					case 'categoria':
						return self::novo_categoria($base);
					break;

					default:
						return self::novo_produto($base);
					break;
				}
			else:
				return self::novo_produto($base);			
			endif;	
		}

		public static function editar($base)
		{
			if(!empty($_GET['editar'])):	
				$opcao = addslashes($_GET['editar']);
				switch ($opcao) 
				{
					case 'categoria':
						return self::editar_categoria($base);
					break;
					case 'produto':
						return self::editar_produto($base);
					break;								
					default:
						return self::editar_produto($base);
					break;
				}
			else:
				return self::editar_produto($base);			
			endif;	
		}
		
		public static function listar($base)
		{ 
			if(!empty($_GET['lista'])):	
				$opcao = addslashes($_GET['lista']);
				switch ($opcao) {
						case 'categorias':
							return self::lista_categorias($base);
						break;
						case 'produtos':
							return self::lista_produtos($base);
						break;						
						default:
							return self::lista_produtos($base);
						break;
					}
			else:
				return self::lista_produtos($base);			
			endif;	
		}

		public static function cria_tebelas()
		{	
			/* CRIAR TEBELAS NO BANACO */
			$tabelas = array();

			$tabelas[] = array(
				self::$tabela=>
				array(
					'nome'=>1,
					'id_categoria'=>0,
					'descricao'=>2,
					'foto'=>2,
					'preco'=>4,
					'peso'=>4,
					'quantidade'=>0,
					'status'=>0
					));

				$tabelas[] = array(
				self::$tabela_categorias=>
				array(
					'nome'=>1,
					'descricao'=>2,
					'id_cat_pai'=>0,
					'staus'=>0
					));


			return $tabelas;
		}

		public static function novo_produto($base)
		{
			/* GERA HTML PAGINA NOVO */
			/* https://github.com/nellrcs/wgform */
			$form_foto = html_upload_foto(self::$largura,self::$altura,self::$tamnaho_k_bytes);

			$form = new WGform(); 
			$form::$nome = 'formulario';
			$form::$action = 'io/inserir.php';
			$form::$method = "POST";
			$form::$html_botao = $form_foto."<br><button type='submit' class='btn btn-primary'> SALVAR </button>";
			$form::$ajax = array('enviado_redireciona','erro','antes_enviar','completo');
			$paramtros_obj = array();

			$categorias = $base->seleciona(self::$tabela_categorias,null,array('ID','nome'));	
			$campos_categorias = array();
			foreach ($categorias as $v) 
			{
				$lista_categorias[$v->ID] = $v->nome; 
			}

			/* CAMPOS */	
			/* campo 'tabela' obrigatorio */
			$paramtros_obj['tabela'] = $form::index(array('tipo'=>5,'campo'=>'tabela','valor'=>base64_encode(self::$tabela)));
			
			/* capos relativos ao metodo cria_tebelas */
			$paramtros_obj['nome'] = $form::index(array('tipo'=>0,'campo'=>'nome','label'=>true,'html_depois'=>'<br>','titulo'=>'Nome','obrigatorio'=>true));
			$paramtros_obj['preco'] = $form::index(array('tipo'=>0,'campo'=>'preco','label'=>true,'html_depois'=>'<br>','titulo'=>'Preco'));
			$paramtros_obj['descricao'] = $form::index(array('tipo'=>1,'campo'=>'descricao','label'=>true,'html_depois'=>'<br>','titulo'=>'Descricao'));
			$paramtros_obj['quantidade'] = $form::index(array('tipo'=>0,'campo'=>'quantidade','label'=>true,'html_depois'=>'<br>','titulo'=>'Quantidade','mascara'=>'0000000'));
			$paramtros_obj['categoria'] = $form::index(array('tipo'=>2,'opcoes'=>array_merge(array('nao'=>'sem categoria'),$lista_categorias),'campo'=>'id_categoria','label'=>true,'html_depois'=>'<br>','titulo'=>'Categoria'));

			html_botao_add_refresh_lista(self::$tabela,adiciona_ao_get(array('op'=>"novo")));
			echo $form::formulario($paramtros_obj);
		}

		public static function novo_categoria($base)
		{
			/* GERA HTML PAGINA NOVO */
			/* https://github.com/nellrcs/wgform */
			$form = new WGform(); 
			$form::$nome = 'formulario';
			$form::$action = 'io/inserir.php';
			$form::$method = "POST";
			$form::$html_botao = "<br><button type='submit' class='btn btn-primary'> SALVAR </button>";
			$form::$ajax = array('enviado_redireciona','erro','antes_enviar','completo');
			$paramtros_obj = array();


			$categorias = $base->seleciona(self::$tabela_categorias,null,array('ID','nome'));	
			$lista_categorias = array();
			foreach ($categorias as $v) 
			{
				$lista_categorias[$v->ID] = $v->nome; 
			}

			/* CAMPOS */	
			/* campo 'tabela' obrigatorio */
			$paramtros_obj['tabela'] = $form::index(array('tipo'=>5,'campo'=>'tabela','valor'=>base64_encode(self::$tabela_categorias)));
			
			/* capos relativos ao metodo cria_tebelas */
			$paramtros_obj['nome'] = $form::index(array('tipo'=>0,'campo'=>'nome','label'=>true,'html_depois'=>'<br>','titulo'=>'Nome','obrigatorio'=>true));
			$paramtros_obj['id_cat_pai'] = $form::index(array('tipo'=>2,'opcoes'=>array_merge(array('nao'=>'sem categoria'),$lista_categorias),'campo'=>'id_cat_pai','label'=>true,'html_depois'=>'<br>','titulo'=>'Categoria Pai'));

			html_botao_add_refresh_lista('produtos&lista=categorias',adiciona_ao_get(array('op'=>"novo")));
			echo $form::formulario($paramtros_obj);

		}

		public static function editar_produto($base)
		{

			$id = addslashes($_GET['id']);
			$where = array('ID'=>$id); 
			$tabela = self::$tabela;
			$db_serv = $base->seleciona(self::$tabela,$where,null);
			$where = json_encode($where);
			$ln = $db_serv[0];

			$img_atual =  !empty($ln->foto) ? "../uploads/".self::$tabela."/".$ln->foto : "";

			$categorias = $base->seleciona(self::$tabela_categorias,null,array('ID','nome'));	
			$lista_categorias = array();
			foreach ($categorias as $v) 
			{
				if($ln->id_categoria):
					$lista_categorias['@'.$v->ID] = $v->nome; 
				else:
					$lista_categorias[$v->ID] = $v->nome;
				endif;	
			}

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
			$paramtros_obj['nome'] = $form::index(array('tipo'=>0,'campo'=>'nome','label'=>true,'html_depois'=>'<br>','titulo'=>'Nome','obrigatorio'=>true,'valor'=>$ln->nome));
			$paramtros_obj['preco'] = $form::index(array('tipo'=>0,'campo'=>'preco','label'=>true,'html_depois'=>'<br>','titulo'=>'Preco','valor'=>$ln->preco));
			$paramtros_obj['descricao'] = $form::index(array('tipo'=>1,'campo'=>'descricao','label'=>true,'html_depois'=>'<br>','titulo'=>'Descricao','valor'=>$ln->descricao));
			$paramtros_obj['quantidade'] = $form::index(array('tipo'=>0,'campo'=>'quantidade','label'=>true,'html_depois'=>'<br>','titulo'=>'Quantidade','mascara'=>'0000000','valor'=>$ln->quantidade));
			$paramtros_obj['categoria'] = $form::index(array('tipo'=>2,'opcoes'=>array_merge(array('nao'=>'sem categoria'),$lista_categorias),'campo'=>'id_categoria','label'=>true,'html_depois'=>'<br>','titulo'=>'Categoria'));

			html_botao_add_refresh_lista(self::$tabela,adiciona_ao_get(array('op'=>"novo")));

			echo $form::formulario($paramtros_obj);
		}

		public static function editar_categoria($base)
		{

			$id = addslashes($_GET['id']);
			$where = array('ID'=>$id); 
			$tabela = self::$tabela_categorias;
			$db_serv = $base->seleciona(self::$tabela_categorias,$where,null);
			$where = json_encode($where);
			$ln = $db_serv[0];

			$categorias = $base->seleciona(self::$tabela_categorias,null,array('ID','nome'));	
			$lista_categorias = array();
			foreach ($categorias as $v) 
			{
				if($ln->id_cat_pai):
					$lista_categorias['@'.$v->ID] = $v->nome; 
				else:
					$lista_categorias[$v->ID] = $v->nome;
				endif;	
			}

			$form = new WGform(); 
			$form::$nome = 'formulario';
			$form::$action = 'io/update.php';
			$form::$method = "POST";

			$form::$html_botao = "<br><button type='submit' class='btn btn-primary'> EDITAR </button>";

			$form::$ajax = array('enviado_redireciona','erro','antes_enviar','completo');
			$paramtros_obj = array();

			/*CAMPOS*/	
			/*campo 'tabela' obrigatorio */
			$paramtros_obj['tabela'] = $form::index(array('tipo'=>5,'campo'=>'tabela','valor'=>base64_encode($tabela)));
			$paramtros_obj['where'] = $form::index(array('tipo'=>5,'campo'=>'where','valor'=>base64_encode($where)));
			
			/* capos relativos ao metodo cria_tebelas */
			$paramtros_obj['nome'] = $form::index(array('tipo'=>0,'campo'=>'nome','label'=>true,'html_depois'=>'<br>','titulo'=>'Nome','obrigatorio'=>true,'valor'=>$ln->nome));
			$paramtros_obj['id_cat_pai'] = $form::index(array('tipo'=>2,'opcoes'=>array_merge(array('nao'=>'sem categoria'),$lista_categorias),'campo'=>'id_cat_pai','valor'=>$ln->id_cat_pai,'label'=>true,'html_depois'=>'<br>','titulo'=>'Categoria Pai'));

			html_botao_add_refresh_lista('produtos&lista=categorias',adiciona_ao_get(array('op'=>"novo",'novo'=>"categoria"),array('id'=>"",'editar'=>"")));
			echo $form::formulario($paramtros_obj);
		}



		public static function lista_produtos($base)
		{
			$ln = $base->seleciona(self::$tabela,null,null);
			html_topo_pagina(ucfirst(self::$tabela));	
			html_botao_add_refresh(adiciona_ao_get(array('op'=>"novo",'novo'=>'produto')));
			
			if(empty($ln)):
				html_lista_vazia();	
			else:
				self::estrutura_header(array('Imagem','Nome','Quantidade','Opções'));
				foreach($ln as $valor): 
					$camp_id = $valor->ID;	
					$d = array('id'=>$camp_id);
					$where_del = json_encode($d);
		  			$url_imagem = "thumb.php?tipo=red&img=../uploads/".self::$tabela."/".$valor->foto."&amp;w=40&amp;h=40";
					$onclick = "apagar('".base64_encode(self::$tabela)."','".base64_encode($where_del)."','')";
					self::html_item_produto($valor,$url_imagem,$onclick,$camp_id);
				endforeach; 
				self::estrutura_footer();
		 	endif;
		 	
		}


		public static function lista_categorias($base)
		{
			$ln = $base->seleciona(self::$tabela_categorias,null,null);
			html_topo_pagina(ucfirst(self::$tabela_categorias));	
			html_botao_add_refresh(adiciona_ao_get(array('op'=>"novo",'novo'=>'categoria'),array('lista'=>"") ));
	
			if(empty($ln)):
				html_lista_vazia();	
			else:
				self::estrutura_header(array('#ID','Nome','Opções'));
				foreach($ln as $valor): 
					$camp_id = $valor->ID;	
					$d = array('id'=>$camp_id);
					$where_del = json_encode($d);
					$onclick = "apagar('".base64_encode(self::$tabela_categorias)."','".base64_encode($where_del)."','')";
					self::html_item_categoria($valor,$onclick,$camp_id);
				endforeach; 
				self::estrutura_footer();
		 	endif;
		}

		public static function estrutura_header($header = array())
		{
			echo "<table class='table table-striped table-hover table-bordered' ><thead><tr>";
			foreach ($header as $c) 
			{
				echo "<th>".$c."</th>";
			}
			echo "</tr></thead>";
			echo "<tbody>";
		}

		public static function estrutura_footer()
		{
			echo "</tbody></table>";
		}

		public static function html_item_produto($valor,$url_imagem,$onclick,$camp_id)
		{
		  	echo "<tr>";
		    echo " <td><img src=".$url_imagem."></td>";
		    echo " <td>".$valor->nome."</td>";
		    echo " <td><span class='label label-default'>".$valor->quantidade."</span></td>";
		   	echo ' <td width="15%"><a href="'.adiciona_ao_get(array('op'=>"editar",'id'=>$camp_id)).'" class="btn btn-primary btn-sm" role="button"><i class="glyphicon glyphicon-edit"></i></a>';
		  	echo ' <a onclick="'.$onclick.'" class="btn btn-danger btn-sm" role="button"><i class="glyphicon glyphicon-trash"></i></a></td>';
		    echo "</tr>";

		}

		public static function html_item_categoria($valor,$onclick,$camp_id)
		{
		  	echo "<tr>";
		    echo " <td>".$valor->ID."</td>";
		    echo " <td>".$valor->nome."</td>";
			echo ' <td width="15%"><a href="'.adiciona_ao_get(array('op'=>"editar",'editar'=>"categoria",'id'=>$camp_id),array('lista'=>"")).'" class="btn btn-primary btn-sm" role="button"><i class="glyphicon glyphicon-edit"></i></a>';
		  	echo ' <a onclick="'.$onclick.'" class="btn btn-danger btn-sm" role="button"><i class="glyphicon glyphicon-trash"></i></a></td>';
		    echo "</tr>";

		}

		public static function scripts()
		{}

	} 

?>
