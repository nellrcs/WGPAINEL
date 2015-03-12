<?php 
	function adiciona_ao_get($nov = array(),$remov = array())
	{
		$url = "?";
		if(!empty($_GET)):

			$ark = array_merge($_GET,$nov);

			if(!empty($remov)):
				$ark = array_diff_key($ark,$remov);
			endif;

			foreach($ark as $chave => $valor):
				if( reset($ark) != $valor ):
					$url .= "&";
				endif;	
				$url .=  $chave."=".$valor;
			endforeach;	
			return $url;
		else:
			foreach($nov as $chave => $valor):
				if( reset($ark) != $valor ):
					$url .= "&";
				endif;	
				$url .=  $chave."=".$valor;
			endforeach;	
			return $url;
		endif;
	}


	function lista_paginas_menu($nomes_menu,$limite_menu = 2)
	{
	    $extra_menu = array();
	    $n_array = count($nomes_menu);

	    if($n_array > $limite_menu):
	      for ($i=$limite_menu; $i < $n_array; $i++) 
	      { 
	        $extra_menu[] = $nomes_menu[$i];
	      } 
	    endif;
	    $cont = 0;
	    foreach ($nomes_menu as $nome)
	    {
	      if(is_array($nome)): 

	        foreach ($nome as $c => $v) 
	        { 
	            if(is_array($v)):
	            ?>
	            <li class="dropdown">
	              <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><?php echo $c; ?><b class="caret"></b></a>
	              <ul class="dropdown-menu">
	                 <?php 
	                 foreach ($v as $c2 => $v2) {
	                   echo "<li><a href='?pagina=".$v2."'>".ucfirst($c2)."</a></li>";
	                 }?> 
	              </ul>
	            </li>
	            <?php
	            else:
	              echo "<li><a href='?".$v."'>".ucfirst($c)."</a></li>";
	            endif;
	        }
	      else:  
	        echo "<li><a href='?pagina=".$nome."'>".ucfirst($nome)."</a></li>";
	      endif;
	      $cont++;
	      if($cont >= $limite_menu):
	        break;
	      endif;  
	    }
	    ?>
	    <?php if(!empty($extra_menu)): ?>
	    <li class="dropdown multi-level">
	      <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-plus"></i>&nbsp;Mais<b class="caret"></b></a>
	      <ul class="dropdown-menu">
	      <?php 
	          foreach ($extra_menu as $nome_extra)
	          {
	            if(is_array($nome_extra)): 
	              foreach ($nome_extra as $c => $v) 
	              { 
	                  if(is_array($v)):
	                  ?>
	                  <li class="dropdown-submenu">
	                    <a tabindex="-1" href="#"><?php echo $c; ?></a>
	                    <ul class="dropdown-menu">
	                       <?php 
	                       foreach ($v as $c2 => $v2) {
	                         echo "<li><a href='?pagina=".$v2."'>".ucfirst($c2)."</a></li>";
	                       }?> 
	                    </ul>
	                  </li>
	                  <?php
	                  else:
	                    echo "<li><a href='?".$v."'>".ucfirst($c)."</a></li>";
	                  endif;
	              }
	            else:  
	              echo "<li><a href='?pagina=".$nome_extra."'>".ucfirst($nome_extra)."</a></li>";
	            endif;

	          }
	       ?>
	      </ul>
	    </li>
	<?php endif; 
	}


	function lista_textos_menu()
	{
		$con_textos = new Conexao();
		$total_textos = $con_textos->seleciona('textos',array('sigla_idioma'=>''),array('titulo','tipo'));
		if(!empty($total_textos)):
		?>	
			<li class="dropdown">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">Textos<b class="caret"></b></a>
            <ul class="dropdown-menu">
	        <?php foreach($total_textos as $ln){ ?>
	            <li><a href="?pagina=sistema&alt=textos&tipo=<?= $ln->tipo; ?>"><?= $ln->titulo; ?></a></li>  
			<?php  } ?>		        
            </ul>
            </li>
		<?php
		endif;	
	}


	function get_op_modo()
	{

		if(!empty($_GET['op'])):	
			$opcao = addslashes($_GET['op']);
			if($opcao == "editar"):
				if(!empty($_GET['id'])):
					$modo = "editar";
				else:
					$modo = "listar";
				endif;
			elseif($opcao == "novo"): 
				$modo = "novo";
			else:
				$modo = $modo;
			endif;
			return $modo;
		else:
			return "listar";	
		endif;	
	}

	function html_lista_idiomas()
	{
		$ob_conexao = new Conexao();
		$total_idiomas = $ob_conexao->seleciona('idiomas',array('status'=>'1'));
		if($total_idiomas): ?>
		    <div class="row">
		        <pre class="col-lg-4"><?php
		            foreach ($ob_conexao->seleciona('idiomas',array('status'=>'1'),null) as  $val):
		                echo "<a href='".adiciona_ao_get(array('lang'=>$val->sigla))."' class='nacoes ".$val->sigla."' title='".$val->nome."' ></a>";
		            endforeach;
		        ?><a href="<?= adiciona_ao_get(array('lang'=>'br'),array('lang'=>'')) ?>" class="nacoes br" title='Portugues' ></a></pre>
		    </div>
		<?php    
		endif; 
	}

	function html_topo_pagina($titulo = null)
	{
		?>
		<div class="row">
			<h1><?= $titulo ?></h1>
		</div>
		<br>
		<?php
	}


	function html_botao_add_refresh($link)
	{
		?>
		<div class="row">
			<a class="btn btn-primary"  onclick="location.reload()"><i class="glyphicon glyphicon-refresh"></i></a>
			<a href="<?= $link; ?>" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i>Adicionar Novo</a>
			</div>
		<br>
		<?php
	}

	function html_botao_add_refresh_lista($pagina,$link)
	{
		?>
		<div class="row">
			<a class="btn btn-primary"  onclick="location.reload()"><i class="glyphicon glyphicon-refresh"></i></a>
			<a href="?pagina=<?= $pagina; ?>" class="btn btn-default"><i class="glyphicon glyphicon-list"></i></a>
			<a href="<?= $link; ?>" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i> Adicionar Novo</a>
		</div>
		<br>
		<?php
	}



	function html_botao_refresh_lista($pagina)
	{
		?>
		<div class="row">
			<a class="btn btn-primary"  onclick="location.reload()"><i class="glyphicon glyphicon-refresh"></i></a>
			<a href="?pagina=<?= $pagina; ?>" class="btn btn-default"><i class="glyphicon glyphicon-list"></i></a>
		</div>
		<br>
		<?php
	}



	function html_lista_vazia()
	{
		?>
			<div class="panel panel-default">
				<div class="panel-body">
					Ainda não existem campos cadastrados.
				</div>
			</div>
		<?php	
	}


	function html_upload_foto($largura,$altura,$tamanho,$img_atual = null)
	{
        ob_start();      
		?>
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="col-lg-5">	
					<label id="image_preview" style="overflow: hidden;" for="foto">
					<div id="regua"></div>
					<img id="previewing" src="<?= $img_atual; ?>" alt="">
				</label>
				</div>
					<div class="col-lg-7">
					<div id="message"></div>
					<br>
					<pre><span style="margin: 10px;">Recomendado (<strong id="largura"><?= $largura; ?></strong>x<strong id="altura"><?= $altura; ?></strong> )px e ate <strong id="tamanho"><?= $tamanho; ?></strong>kb </span></pre>
					<input type="file" name="foto" id="foto" class="form-control"  />	
					<input type="hidden" id="img_atual" name="img_atual" value="<?= $img_atual; ?>"  />	
				</div>
			</div>
		</div>
		<?php
        $html = ob_get_contents();
        ob_get_clean();  
        return $html;
	}
 ?>