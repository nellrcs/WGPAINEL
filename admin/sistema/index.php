<?php 

	$base = new Conexao(); 

	$alt = isset($_GET['alt']) ? $_GET['alt'] : "";
	switch ($alt) {
		case 'email':
			include 'sistema/email.php';
		break;

		case 'endereco':
			include 'sistema/endereco.php';
		break;

		case 'seo':
			include 'sistema/seo.php';
		break;

		case 'idiomas':
			include 'sistema/idiomas.php';
		break;

		case 'redes_sociais':
			include 'sistema/redes_sociais.php';
		break;

		case 'palavras':
			include 'sistema/palavras.php';
		break;

		case 'usuarios':
			include 'sistema/usuarios.php';
		break;

		case 'textos':
			include 'sistema/textos.php';
		break;

		default:
			include 'erro.php';
		break;
	}

?>


	
	