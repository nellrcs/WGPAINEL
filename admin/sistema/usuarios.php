<?php 
	$base = new Conexao(); 
	$tabela = 'usuarios';
	$execulta = isset($_GET['exe']) ? $_GET['exe'] : "";
?>

<div class="row">
  <a href="<?= adiciona_ao_get(array('exe'=>'novo'),array('id'=>'') ) ?>" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i></a>
  <a href="<?= adiciona_ao_get(array('exe'=>'lista'),array('id'=>'') ) ?>" class="btn btn-default"><i class="glyphicon glyphicon-list"></i></a>
</div>
<br>

<?php if ($execulta == 'novo'): 

	
	if(NIVEL_USUARIO == 5):	
		$arry_dados =  array();
		if(!empty($_POST)): 
			$arry_dados['nome'] = isset($_POST['nome']) ? addslashes($_POST['nome']) : "";
			$arry_dados['login'] = isset($_POST['login']) ? addslashes($_POST['login']) : "";
			$arry_dados['senha'] = isset($_POST['senha']) ? md5(addslashes($_POST['senha'])) : "";
			$insert = $base->inserir($tabela,$arry_dados);
			if($insert):	
			?>	
				 <script>window.location = "index.php<?= adiciona_ao_get(array('exe'=>'lista')) ?>";</script>
			<?php	
			endif;
		endif;
	?>	
		<form action="" method="POST">
			<h1>Novo usuario</h1>
			<div class="col-lg-6">
				<br>
				<label for="">Nome:</label>
					<input type="text" name="nome" class="form-control" required>
				</div>
			<div class="col-lg-6">
				<br>
				<label for="">Login:</label>		
				<input type="text" name="login" class="form-control" required>
			</div>
			

			<div class="col-lg-6">
			     <div class="form-group">
			     <br>
			        <label for="">Senha:</label>
			        <div class="input-group">
			          <input type="text" class="form-control" name="senha" rel="gp" data-size="8"  data-character-set="a-z,A-Z,0-9,#" required>
			          <span class="input-group-btn"><button type="button" class="btn btn-default getNewPass"><span class="glyphicon glyphicon-refresh"></span></button></span>
			        </div>
			      </div>
			</div>

			<div class="col-lg-12">
			<br>
				<button type="submit" class="btn btn-primary">ENVIAR</button>	
			</div>

	</form>
	<?php 
	else:
		echo '<div class="alert alert-dismissable alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>Permissão negada !</div>';
	endif; 
	?>
<?php elseif($execulta == 'deleta'): 
if(NIVEL_USUARIO == 5):	
$id = isset($_GET['id']) ? addslashes($_GET['id']) : "";
if((int)$id):
	$del = $base->apaga($tabela,array('ID'=>$id));
	if($del): ?>
		<script>window.location = "index.php<?= adiciona_ao_get(array('exe'=>'lista')) ?>";</script>
	<?php
	endif;	
endif; 
else:
	echo '<div class="alert alert-dismissable alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>Permissão negada !</div>';
endif; 
?>
	
<?php elseif($execulta == 'edita'):
if(NIVEL_USUARIO == 5):	
	$id = isset($_GET['id']) ? addslashes($_GET['id']) : "";

	if((int)$id):
		$users = $base->seleciona($tabela,array('ID'=>$id));
		if($users):
			$users = isset($users[0]) ? $users[0] : new stdClass();
			$arry_dados =  array();
			if(!empty($_POST)): 
				$arry_dados['nome'] = isset($_POST['nome']) ? addslashes($_POST['nome']) : "";

				$arry_dados['login'] = isset($_POST['login']) ? addslashes($_POST['login']) : "";

				$arry_dados['senha'] = !empty($_POST['senha']) ? md5(addslashes($_POST['senha'])) : $users->senha;
 
				$edita = $base->atualiza($tabela,$arry_dados,array('ID'=>$id));

				if($edita):	
				?>	
					<script>window.location = "index.php<?= adiciona_ao_get(array('exe'=>'lista')) ?>";</script>
				<?php	
				endif;
			else:
				$arry_dados =  array('nome'=>$users->nome,'login'=>$users->login,'senha'=>'');	
			endif;	
		endif;	
	endif; 

?>

	<form action="" method="POST">
			<h1>Novo usuario</h1>
			<div class="col-lg-6">
				<br>
				<label for="">Nome:</label>
					<input type="text" name="nome" class="form-control" value="<?= $arry_dados['nome'] ?>" required>
				</div>
			<div class="col-lg-6">
				<br>
				<label for="">Login:</label>		
				<input type="text" name="login" class="form-control" value="<?= $arry_dados['login'] ?>" required>
			</div>
			

			<div class="col-lg-6">
			     <div class="form-group">
			     <br>
			        <label for="">Senha:</label>
			        <div class="input-group">
			          <input type="text" class="form-control" name="senha" rel="gp" data-size="8"  data-character-set="a-z,A-Z,0-9,#" >
			          <span class="input-group-btn"><button type="button" class="btn btn-default getNewPass"><span class="glyphicon glyphicon-refresh"></span></button></span>
			        </div>
			      </div>
			</div>

			<div class="col-lg-12">
			<br>
				<button type="submit" class="btn btn-primary">ENVIAR</button>	
			</div>
	</form>
<?php
else:
	echo '<div class="alert alert-dismissable alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>Permissão negada !</div>';
endif; 
?>		
<?php else: 
$users =  $base->seleciona($tabela,null,array('ID','nome'));
 if(empty($users)): ?>
	<div class="panel panel-default">
		<div class="panel-body">
		Ainda nao existem <?= $tabela ?> cadastrados.
		</div>
	</div>
<?php endif; 
foreach ($users as $c => $v): ?>
	<div class="panel panel-default">
	  <div class="panel-body">
	    <strong><?= $v->nome; ?></strong>
		<div class="btn-group pull-right" role="group" aria-label="...">
		  <a href="<?= adiciona_ao_get(array('exe'=>'edita','id'=> $v->ID)) ?>" class="btn btn-sm btn-default"><i class="glyphicon glyphicon-edit"></i></a>
		  <a href="<?= adiciona_ao_get(array('exe'=>'deleta','id'=> $v->ID)) ?>" class="btn btn-sm btn-danger"><i class="glyphicon glyphicon-trash"></i></a>
		</div>
	  </div>
	</div>
	<?php endforeach; ?>
<?php endif; ?>


  <script>
		// Generate a password string
		function randString(id){
		  var dataSet = $(id).attr('data-character-set').split(',');  
		  var possible = '';
		  if($.inArray('a-z', dataSet) >= 0){
		    possible += 'abcdefghijklmnopqrstuvwxyz';
		  }
		  if($.inArray('A-Z', dataSet) >= 0){
		    possible += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		  }
		  if($.inArray('0-9', dataSet) >= 0){
		    possible += '0123456789';
		  }
		  if($.inArray('#', dataSet) >= 0){
		    possible += '![]{}()%&*$#^<>~@|';
		  }
		  var text = '';
		  for(var i=0; i < $(id).attr('data-size'); i++) {
		    text += possible.charAt(Math.floor(Math.random() * possible.length));
		  }
		  return text;
		}

		// Create a new password on page load
/*		$('input[rel="gp"]').each(function(){
		  $(this).val(randString($(this)));
		});*/

		// Create a new password
		$(".getNewPass").click(function(){
		  var field = $(this).closest('div').find('input[rel="gp"]');
		  field.val(randString(field));
		});

		// Auto Select Pass On Focus
		$('input[rel="gp"]').on("click", function () {
		   $(this).select();
		});

</script>