<?php 
 /* 
 *  @author    Warllen castro dos santos.
 *  @license   http://www.apache.org/licenses/LICENSE-2.0
 */	
class UrlAmigavel
{
	//RETORNA TODOS OS CAMPOS DA URL OU UMA POSICAO ESPECIFICA.
	public static function campo_url($posicao = null)
	{	
		//pega o caminho inteiro
		$total_de_barras = explode("/", str_replace(strrchr($_SERVER["REQUEST_URI"], "?"), "", $_SERVER["REQUEST_URI"]));	
		//pega somente os diretorios
		$barras_amigaveis = explode("/", str_replace(strrchr($_SERVER["PHP_SELF"], "?"), "", $_SERVER["PHP_SELF"]));	

		// verifica se depois da ultima '/' existe  algum campo
		if($total_de_barras[count($total_de_barras) -1] == '')
		{
			//remove o ultimo campo se for vazio	
			unset($total_de_barras[count($total_de_barras) -1]);
		}

		//retorna a url amigavel como array
		$retorno = array_slice($total_de_barras, count($barras_amigaveis) - 1);

		if($posicao != null || $posicao === 0)
		{
			//retorna somente a posicao Ex:. campo_url(0) = pagina/ campo_url(1) = id
 			return $retorno[$posicao];
		}	
		else
		{	
			return $retorno;	
		}	
	}

	//SE FOR PASSADO O NOME DO CAMPO ELE RETORNA O VALOR DO CAMPO EX: pagina=home / se existir a pagina ele retornara home	
	//SE  NAO FOR PASSADO O NOME DO CAMPO ELE RETORNA UM ARAY: [0]home [1]produt [2]22 [3]
	public static function url_get($campo = null)
	{
	
		 if($campo != null)
		 {
		 	if(!empty($_GET[$campo]))
		 	{
		 		$retorno = $_GET[$campo];	
		 	}
		 	else
		 	{
		 		$retorno = false;	
		 	}	
		 }	
		 else
		 {
		 	if(!empty($_GET))
		 	{
		 		
		 		foreach ($_GET as $valor) 
		 		{
		 			$pos = strpos($valor, '/');
		 			if($pos === false)
		 			{
		 				$retorno = array_values($_GET);
		 			}
		 			else
		 			{
		 				$retorno = self::campo_url();
		 				break;
		 			}	
		 		}
		 		
		 	}	
		 	else
		 	{
		 		$retorno = false;	
		 	}	
		 }	

		 return $retorno;

	}

	//VALIDA OS CAMPOS DA URL SE ELES EXSTEM NO INCLUDE DA PAGINA
	public static function valida_a_url($url_total, $url_master,$numero_de_oks)
	{

		$numro_da_base = count($url_master);

		$i = array();
		
		//valida a url gradtivamente se precisar validar ela inteira
		//cria novo codigo
		if(count($url_total) < $numero_de_oks)
		{
			$numero_de_oks = count($url_total);	
		}	


		foreach ($url_total as $key => $url_separada) 
		{

			if($key < $numro_da_base)
			{	
				foreach ($url_master[$key] as $valor) 
				{

					if($url_separada == $valor)
					{
						$i[] = $valor;
					}

				}
			}

		}

		if(count($i) ==  $numero_de_oks)
		{
			
			return $i;
		}
		else	
		{			
			return false;
		}	
	}


	public static function url_padrao($tipo = 'http')
	{
		$barras_amigaveis = explode("/", str_replace(strrchr($_SERVER["PHP_SELF"], "?"), "", $_SERVER["PHP_SELF"]));  
		unset($barras_amigaveis[count($barras_amigaveis) -1]);
		if($barras_amigaveis[0] == "")
		{
			unset($barras_amigaveis[0]);
		}
		$tipo = !empty($tipo) ? $tipo : 'http'; 
		$ds  = "/";
		$n = implode($barras_amigaveis, $ds);
		
		if(!empty($n))
		{
			return $tipo."://".$_SERVER['SERVER_NAME'] . $ds.$n.$ds;
		}
		else
		{
			return $tipo."://".$_SERVER['SERVER_NAME'] . $ds;
		}	
	}

	public static function execultar($campos_orden = null,$n_campos_validos = null)
	{
		if(self::url_get() == false || !empty($n_campos_validos))
		{
			if($campos_orden != null && $n_campos_validos != null)
			{
				$retorno  = self::valida_a_url(self::campo_url(), $campos_orden,$n_campos_validos);
			}
			else
			{
				$retorno  =  self::campo_url();
			}	
		}	
		else
		{
			$retorno  = self::url_get();
		}	

		return $retorno;
	}


	public static function gera_htaccess()
	{

		$arquivo = fopen(".htaccess", "w");
		$htaccess = "";
		$htaccess.="RewriteEngine On"."\r\n";
		$htaccess.=	"RewriteCond %{SCRIPT_FILENAME} !-f"."\r\n";
		$htaccess.=	"RewriteCond %{SCRIPT_FILENAME} !-d"."\r\n";
		$htaccess.=	"RewriteCond $1 !^(index\.php|img|css|js|favicon\.ico|robots\.txt)"."\r\n";
		$htaccess.=	"\r\n";
		$htaccess.=	"RewriteRule ^(.*)$ index.php?indice=$1"."\r\n";
		$grava = fwrite($arquivo, $htaccess);
		fclose($arquivo);
		echo "<script>alert('um arquivo .htaccess foi gerado..')</script>";

	}

}

//Ex.
/*$url_master_array = array(

 array("configuracoes"),
 array("pagina", "plugin"),
 array("ativar", "desativar", "instalar"),
 array("0", "1"),
 array("pt", "es", "en")

	);*/

//URL VALIDADA
//$possibilidades = 5;
//print_r(UrlAmigavel::execultar($url_master_array,$possibilidades));


//URL NORMAl
//print_r(UrlAmigavel::execultar());


?>