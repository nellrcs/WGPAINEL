<?php
	class loja
	{
		public static $tabela = "produtos";
		public static $tabela_categorias = "categorias";
		public static $tabela_pedidos = "pedidos";
		public static $tabela_clientes = "clientes";
		public static $tabela_pagamento = "pagseguro";
		public static $pedidos_produtos = "pedidos_produtos";
		
		public static $largura = 800;
		public static $altura = 600;
		public static $tamnaho_k_bytes = 300;

		public static $status_pedido = array(0=>'aguardando',1=>'cancelado',2=>'completo',3=>'saio para entrega',4=>'entregue');
		public static $menu_nome = 
		array(
			'Loja'=>array(
			'Categorias'=>'loja&lista=categorias',
			'Produtos'=>'loja&lista=produtos',
			'Pedidos'=>'loja&lista=pedidos',
			'Clientes'=>'loja&lista=clientes',
			'Config. Pagamento'=>'loja&op=editar&editar=pagamento&id=1'
			));

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

					case 'pedido':
						return self::editar_pedido($base);
					break;
					case 'produto':
						return self::editar_produto($base);
					break;	
					case 'cliente':
						return self::editar_cliente($base);
					break;		
					case 'pagamento':
						return self::editar_pagamento($base);
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

						case 'pedidos':
							return self::lista_pedidos($base);
						break;

						case 'produtos':
							return self::lista_produtos($base);
						break;						
						
						case 'clientes':
							return self::lista_clientes($base);
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
					'quantidade'=>0,
					'peso'=>0,
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

				$tabelas[] = array(
				self::$tabela_clientes=>
				array(
					'nome'=>1,
					'email'=>1,
					'rg'=>1,
					'cpf'=>1,
					'password'=>1,
					'telefone'=>1,
					'estado'=>1,
					'cidade'=>1,
					'cep'=>1,
					'rua'=>1,
					'bairro'=>1,
					'complemento'=>1,
					'numero'=>1
					));

				$tabelas[] = array(
				self::$tabela_pedidos=>
				array(
					'status'=>2,
					'data'=>3,
					'id_cliente'=>0,
					'total'=>4,
					'descricao'=>2
					));

				$tabelas[] = array(
				self::$pedidos_produtos=>
				array(
					'id_pedido'=>0,
					'id_produto'=>0,
					'quantidade'=>0,
					'nome_produto'=>1,
					'preco'=>4,
					'peso'=>4
					));

				$tabelas[] = array(
				self::$tabela_pagamento=>
				array(
					'email'=>1,
					'token'=>2,
					'url_retorno'=>2
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
			$lista_categorias = array();
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
			$paramtros_obj['peso'] = $form::index(array('tipo'=>0,'campo'=>'peso','label'=>true,'html_depois'=>'<br>','titulo'=>'Peso gramas.'));
			$paramtros_obj['descricao'] = $form::index(array('tipo'=>1,'campo'=>'descricao','label'=>true,'html_depois'=>'<br>','titulo'=>'Descricao'));
			$paramtros_obj['quantidade'] = $form::index(array('tipo'=>0,'campo'=>'quantidade','label'=>true,'html_depois'=>'<br>','titulo'=>'Quantidade','mascara'=>'0000000'));
			$paramtros_obj['categoria'] = $form::index(array('tipo'=>2,'opcoes'=>array_merge(array('nao'=>'sem categoria'),$lista_categorias),'campo'=>'id_categoria','label'=>true,'html_depois'=>'<br>','titulo'=>'Categoria'));

			html_botao_add_refresh_lista('loja&lista='.self::$tabela,adiciona_ao_get(array('op'=>"novo")));

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

			html_botao_add_refresh_lista('loja&lista='.self::$tabela_categorias,adiciona_ao_get(array('op'=>"novo")));
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
			$paramtros_obj['peso'] = $form::index(array('tipo'=>0,'campo'=>'peso','label'=>true,'html_depois'=>'<br>','titulo'=>'Peso gramas.','valor'=>$ln->peso));
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

			html_botao_add_refresh_lista('loja&lista=categorias',adiciona_ao_get(array('op'=>"novo",'novo'=>"categoria"),array('id'=>"",'editar'=>"")));
			echo $form::formulario($paramtros_obj);
		}

		public static function editar_pagamento($base)
		{
			

			$where = array('ID'=>1); 
			$tabela = self::$tabela_pagamento;
			$db_serv = $base->seleciona(self::$tabela_pagamento,$where,null);
			$where = json_encode($where);
			
			$form = new WGform(); 

			if($db_serv):
				$ln = $db_serv[0];
				$form::$action = 'io/update.php';
				$paramtros_obj['where'] = $form::index(array('tipo'=>5,'campo'=>'where','valor'=>base64_encode($where)));
			else:	
				$ln = new stdClass();
				$ln->email = "";
				$ln->token = "";
				$form::$action = 'io/inserir.php';
			endif;	


			$form::$nome = 'formulario';
			$form::$method = "POST";

			$form::$html_botao = "<br><button type='submit' class='btn btn-primary'> EDITAR </button>";
			$form::$ajax = array('enviado_redireciona','erro','antes_enviar','completo');
			$paramtros_obj = array();
			
			$paramtros_obj['tabela'] = $form::index(array('tipo'=>5,'campo'=>'tabela','valor'=>base64_encode($tabela)));
			$paramtros_obj['where'] = $form::index(array('tipo'=>5,'campo'=>'where','valor'=>base64_encode($where)));
			
			$paramtros_obj['email'] = $form::index(array('tipo'=>0,'campo'=>'email','label'=>true,'html_depois'=>'<br>','titulo'=>'Email','valor'=>$ln->email));
			$paramtros_obj['token'] = $form::index(array('tipo'=>0,'campo'=>'token','label'=>true,'html_depois'=>'<br>','titulo'=>'Tokem','valor'=>$ln->token));
			$paramtros_obj['url_retorno'] = $form::index(array('tipo'=>0,'campo'=>'url_retorno','label'=>true,'html_depois'=>'<br>','titulo'=>'URL de retorno','valor'=>$ln->url_retorno));
						
			html_topo_pagina('Configuração Pagseguro');	
			html_botao_refresh_lista('loja&lista=produtos');
			
			echo $form::formulario($paramtros_obj);

		}


		public static function editar_pedido($base)
		{

			$id = addslashes($_GET['id']);
			$where = array('ID'=>$id); 
			$tabela = self::$tabela_pedidos;
			$db_serv = $base->seleciona(self::$tabela_pedidos,$where,null);
			$where = json_encode($where);
			$dados_pedido = $db_serv[0];

			$valor = new stdClass();

			$cliente = $base->seleciona(self::$tabela_clientes,array('ID'=>$dados_pedido->id_cliente),null);
			$relacional = $base->seleciona(self::$pedidos_produtos,array('id_pedido'=>$dados_pedido->ID),null);
			$cli = $cliente[0];

			$valor->nome = $cli->nome;
			$valor->email = $cli->email;
			$valor->rg = $cli->rg;
			$valor->cpf = $cli->cpf;
			$valor->telefone = $cli->telefone;
			$valor->estado = $cli->estado;
			$valor->cidade = $cli->cidade;
			$valor->rua = $cli->rua;
			$valor->bairro = $cli->bairro;
			$valor->complemento = $cli->complemento;
			$valor->numero = $cli->numero;
			$valor->cep = $cli->cep;
			$valor->produtos = $relacional;
			$valor->total = $dados_pedido->total;

			$staus = array();
			foreach (self::$status_pedido as $k => $v) 
			{
				if($dados_pedido->status == $k):
					$staus['@'.$k] = $v; 
				else:
					$staus[$k] = $v;
				endif;	
			}
			html_topo_pagina('Pedido');	

			html_botao_refresh_lista("loja&lista=pedidos");


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
			$paramtros_obj['status'] = $form::index(array('tipo'=>2,'campo'=>'status','opcoes'=>$staus,'label'=>true,'html_depois'=>'<br>','html_antes'=>self::html_editar_pedido($valor),'titulo'=>'Situação','obrigatorio'=>true));
			$paramtros_obj['descricao'] = $form::index(array('tipo'=>1,'campo'=>'descricao','label'=>true,'html_depois'=>'<br>','titulo'=>'Descricao','valor'=>$dados_pedido->descricao));
	
			echo $form::formulario($paramtros_obj);
		}

		public static function editar_cliente($base)
		{
			
			$id = addslashes($_GET['id']);
			$where = array('ID'=>$id); 
			$tabela = self::$tabela_clientes;
			$db_serv = $base->seleciona(self::$tabela_clientes,$where,null);
			$where = json_encode($where);
			$ln = $db_serv[0];

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
			$paramtros_obj['nome'] = $form::index(array('tipo'=>0,'campo'=>'nome','label'=>true,'html_depois'=>'<br>','titulo'=>'Nome','valor'=>$ln->nome));
			$paramtros_obj['rg'] = $form::index(array('tipo'=>0,'campo'=>'rg','label'=>true,'html_depois'=>'<br>','titulo'=>'Rg','valor'=>$ln->rg));
			$paramtros_obj['cpf'] = $form::index(array('tipo'=>0,'campo'=>'cpf','label'=>true,'html_depois'=>'<br>','titulo'=>'Cpf','valor'=>$ln->cpf));
			$paramtros_obj['telefone'] = $form::index(array('tipo'=>0,'campo'=>'telefone','label'=>true,'html_depois'=>'<br>','titulo'=>'Telefone','valor'=>$ln->telefone));
			$paramtros_obj['estado'] = $form::index(array('tipo'=>0,'campo'=>'estado','label'=>true,'html_depois'=>'<br>','titulo'=>'Estado','valor'=>$ln->estado));
			
			$paramtros_obj['cidade'] = $form::index(array('tipo'=>0,'campo'=>'cidade','label'=>true,'html_depois'=>'<br>','titulo'=>'Cidade','valor'=>$ln->cidade));
			$paramtros_obj['cep'] = $form::index(array('tipo'=>0,'campo'=>'cep','label'=>true,'html_depois'=>'<br>','titulo'=>'Cep','valor'=>$ln->cep));
			$paramtros_obj['rua'] = $form::index(array('tipo'=>0,'campo'=>'rua','label'=>true,'html_depois'=>'<br>','titulo'=>'Rua','valor'=>$ln->rua));
			$paramtros_obj['bairro'] = $form::index(array('tipo'=>0,'campo'=>'bairro','label'=>true,'html_depois'=>'<br>','titulo'=>'Bairro','valor'=>$ln->bairro));
			$paramtros_obj['complemento'] = $form::index(array('tipo'=>0,'campo'=>'complemento','label'=>true,'html_depois'=>'<br>','titulo'=>'Complemento','valor'=>$ln->complemento));
			$paramtros_obj['numero'] = $form::index(array('tipo'=>0,'campo'=>'numero','label'=>true,'html_depois'=>'<br>','titulo'=>'Numero','valor'=>$ln->numero));
			

			$paramtros_obj['Password'] = $form::index(array('tipo'=>7,'campo'=>'Password','label'=>true,'html_depois'=>'<br>','titulo'=>'Password','valor'=>$ln->password));

			html_topo_pagina('Cliente');	

			html_botao_refresh_lista("loja&lista=clientes");

			echo $form::formulario($paramtros_obj);
		}

		public static function lista_produtos($base)
		{
			$ln = $base->seleciona(self::$tabela,null,null);
			html_topo_pagina(ucfirst(self::$tabela));	
			html_botao_add_refresh(adiciona_ao_get(array('op'=>"novo",'novo'=>'produto'),array('lista'=>"") ));
	
			
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

		public static function lista_pedidos($base)
		{
			
			$ln = $base->seleciona(self::$tabela_pedidos,null,null,array('DESC'=>'data'));
			html_topo_pagina(ucfirst(self::$tabela_pedidos));	
		
			if(empty($ln)):
				html_lista_vazia();	
			else:
				self::estrutura_header(array('#codigo','Situação','Data','Opções'));
				foreach($ln as $valor): 
					$camp_id = $valor->ID;	
					$d = array('id'=>$camp_id);
					$where_del = json_encode($d);
					$onclick = "apagar('".base64_encode(self::$tabela_pedidos)."','".base64_encode($where_del)."','')";
					$status = self::$status_pedido[$valor->status];
					self::html_item_pedido($valor,$onclick,$camp_id,$status);
				endforeach; 
				self::estrutura_footer();
		 	endif;

		}

		public static function lista_clientes($base)
		{
			$ln = $base->seleciona(self::$tabela_clientes,null,null);
			html_topo_pagina(ucfirst(self::$tabela_clientes));	
			
			if(empty($ln)):
				html_lista_vazia();	
			else:
				self::estrutura_header(array('#codigo','Nome','Email','Telefone','Cidade','Opções'));
				foreach($ln as $valor): 
					$camp_id = $valor->ID;	
					$d = array('id'=>$camp_id);
					$where_del = json_encode($d);
					$onclick = "apagar('".base64_encode(self::$tabela_clientes)."','".base64_encode($where_del)."','')";
					self::html_item_cliente($valor,$onclick,$camp_id);
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

		public static function html_item_pedido($valor,$onclick,$camp_id,$status)
		{
		  	echo "<tr>";
		    echo " <td>".$valor->ID."</td>";
		    echo " <td>".$status."</td>";
		    echo " <td>".$valor->data."</td>";
			echo ' <td width="15%"><a href="'.adiciona_ao_get(array('op'=>"editar",'editar'=>"pedido",'id'=>$camp_id),array('lista'=>"")).'" class="btn btn-primary btn-sm" role="button"><i class="glyphicon glyphicon-share"></i></a>';
		  	echo ' <a onclick="'.$onclick.'" class="btn btn-danger btn-sm" role="button"><i class="glyphicon glyphicon-trash"></i></a></td>';
		    echo "</tr>";

		}

		public static function html_editar_pedido($valor)
		{
		?>
			<div class="row">
			<div class="col-lg-6">	
				<h2>Dados do cliente</h2>
				<div><strong>Nome: </strong><?= $valor->nome; ?></div>
				<br>
				<div><strong>Rg/ie: </strong><?= $valor->rg; ?></div>
				<br>
				<div><strong>Cpf/cnpj: </strong><?= $valor->cpf; ?></div>
				<br>
				<div><strong>Telefone: </strong><?= $valor->telefone; ?></div>
				<br>
				<div><strong>Estado: </strong><?= $valor->estado; ?></div>
				<br>
				<div><strong>Cidade: </strong><?= $valor->cidade; ?></div>
				<br>
				<div><strong>Rua: </strong><?= $valor->rua; ?></div>
				<br>
				<div><strong>Bairro: </strong><?= $valor->bairro; ?></div>
				<br>				
				<div><strong>Complemento: </strong><?= $valor->complemento; ?></div>
				<br>
				<div><strong>Numero: </strong><?= $valor->numero; ?></div>
				<br>
				<div><strong>Cep: </strong><?= $valor->cep; ?></div>
				<br>
			</div>
			<div class="col-lg-6">
				<h2>Produtos</h2>
				<?php 
					self::estrutura_header(array('#ID','Nome','Valor','Qtd'));
					(float)$total = 0;
				 ?>
				<?php 
				foreach ($valor->produtos as $produto) 
				{ 
					$total = $total + $produto->preco * $produto->quantidade;
				?>
					<tr>
						<td><?= $produto->ID; ?></td>
						<td><?= $produto->nome_produto; ?></td>
						<td>R$ <?= $produto->preco; ?></td>
						<td><?= $produto->quantidade; ?></td>
					</tr>
				<?php 
				} 
				?>

				<?php self::estrutura_footer(); ?>
				<hr>
				<h2>Total: R$ <?= $valor->total; ?></h2>
			</div>	

			</div>
			<hr>
		<?php	
		}


		public static function html_item_cliente($valor,$onclick,$camp_id)
		{
		  	echo "<tr>";
		    echo " <td>".$valor->ID."</td>";
		    echo " <td>".$valor->nome."</td>";
		    echo " <td>".$valor->email."</td>";
		    echo " <td>".$valor->telefone."</td>";
		    echo " <td>".$valor->cidade."</td>";
			echo ' <td width="15%"><a href="'.adiciona_ao_get(array('op'=>"editar",'editar'=>"cliente",'id'=>$camp_id),array('lista'=>"")).'" class="btn btn-primary btn-sm" role="button"><i class="glyphicon glyphicon-edit"></i></a>';
		  	echo ' <a onclick="'.$onclick.'" class="btn btn-danger btn-sm" role="button"><i class="glyphicon glyphicon-trash"></i></a></td>';
		    echo "</tr>";
		}


		public static function scripts()
		{

		}

	} 

?>
