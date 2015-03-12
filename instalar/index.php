<?php 
include "../config.php";
include "../s_lib/conexao.class.php";

if(!empty($_GET['novo'])):

	$conexao = new Conexao();
	if(file_exists('banco.sql')):
		$arquivo = file('banco.sql');
		$string = ""; 
		foreach($arquivo as $valor):
			$string .= $valor;
		endforeach;
		$sql = explode(";",$string);
		foreach($sql as $query):
			if(!empty($query)):
				$conexao->execulta($query);
			endif;

		endforeach;
	endif;
	echo "CRIADO COM SUCESSO!";
?>

<?php else: ?>
<form action="">
	<h1>Criar tabelaS no banco.</h1>
	<input type="hidden" name="novo" value="1">
	<input type="submit" value="instalar">
</form>
<?php endif; ?>
