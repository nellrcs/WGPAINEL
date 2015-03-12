<?php
 /* 
 *  @author    Warllen castro dos santos.
 *  @license   http://www.apache.org/licenses/LICENSE-2.0
 */	
class Conexao
{
	private $banco = DBBASE;
	private $host = DBHOST;
	private $user = DBUSER;
	private $password = DBPASS;
	private static $pdo;

	function __construct() 
	{

		try 
		{	
			$dsn = "mysql:dbname=".$this->banco.";host=".$this->host;
			$dbh = new PDO($dsn,$this->user,$this->password );
			$this->pdo = $dbh;
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
			return $this->pdo;

		}
		catch ( PDOException $e ) 
		{
			echo 'ERRO: ' . $e->getMessage( );
			return false;
		}
	}
	
	public function seleciona($tabela,$where = array(),$campos = array(), $orden = array() ,$limite = null)
	{
		try
		{
			$string = "SELECT ";
			if(count($campos) > 0):
				$i = 1;
				foreach ($campos as $campo)
				{
					$string .= $campo;
					if ($i < count($campos))
					{
						$string .= ', ';
					}
					$i++;
				}
			else:
				$string .= " * ";
			endif;
			$string .= " FROM ".$tabela;
			if(count($where) > 0):
				$string .= " WHERE ";
				$i = 1;
				foreach($where as $nome => $valor)
				{
					$string .= $nome."='".$valor."'";
					if ($i != count($where))
					{
						$string .= ' AND ';
					}
					$i++;
				}
			endif;

			if(!empty($orden)):
				if(!empty($orden['ASC'])):
					$string .= " ORDER BY ".$orden['ASC']." ASC ";
				elseif(!empty($orden['DESC'])):
					$string .= " ORDER BY ".$orden['DESC']." DESC ";	
				elseif(!empty($orden['RAND'])):
					$string .= " ORDER BY rand() ";	
				endif;	
			endif;	

			if(!empty($limite)):
				$string .= " LIMIT ".$limite;
			endif;	

			$banco = $this->pdo->prepare($string);
			$banco->execute();
			$aR = array();
			while ($lista = $banco->fetch(PDO::FETCH_OBJ)):
				$aR[] = $lista;
			endwhile;
			return $aR;
		}
		catch(PDOExecption $e) 
		{
			echo "ERRO:" . $e->getMessage();
			return false; 
		}
	}

	public function inserir($tabela,$campos = array())
	{
		try 
		{
			$string = "INSERT INTO ". $tabela;
			$string .= " (";
			$i = 1;
			foreach($campos as $nome => $valor)
			{
				$string .= $nome;
				if ($i != count($campos))
				{
					$string .= ', ';
				}
				$i++;
			}
			$string .= ") ";
			$string .= " VALUES (";
			$i = 1;
			foreach($campos as $nome => $valor)
			{
				$string .= '\''.addslashes($valor) . '\' ';
				if ($i != count($campos))
				{
					$string .= ', ';
				}
				$i++;
			}
			$string .= ") ";
			$banco = $this->pdo->prepare($string);
			$banco->execute();
			return $this->pdo->lastInsertId();
		} 
		catch(PDOExecption $e) 
		{
			echo "ERRO:" . $e->getMessage();
			return false; 
		}
	}

	public function atualiza($tabela,$campos = array(),$where = array())
	{
		try 
		{
			$string = "UPDATE ". $tabela . " SET ";
			$i = 1;
			foreach($campos as $campo => $valor)
			{
				$string .= $campo."=".'\''.addslashes($valor).'\'';
				if ($i!= count($campos))
				{
					$string .= ', ';
				}
				$i++;
			}
			$string .= " WHERE ";
			$i = 1;
			foreach($where as $nome => $valor)
			{
				$string .= $nome. "=".'\''.addslashes($valor).'\'';
				if ($i != count($where))
				{
					$string .= ' and ';
				}
				$i++;
			}

			$banco = $this->pdo->prepare($string);
			$banco->execute();
			return true;
		} 
		catch(PDOExecption $e) 
		{
			echo "ERRO:" . $e->getMessage();
			return false; 
		}
	}


	public function apaga($tabela,$where = array())
	{
		try
		{	
			$string = "DELETE FROM ".$tabela;
			$string .= " WHERE ";
			$i = 1;
			foreach($where as $nome => $valor)
			{
				$string .= $nome. "=".'\''.addslashes($valor).'\'';
				if ($i != count($where))
				{
					$string .= ' and ';
				}
				$i++;
			}
			$banco = $this->pdo->prepare($string);
			$banco->execute();
			return true;
		}	
		catch(PDOExecption $e) 
		{
			echo "ERRO:" . $e->getMessage();
			return false; 
		}
	}

	public function execulta($mysql_string)
	{
		$banco = $this->pdo->prepare($mysql_string);
		$banco->execute();
		return $banco;
	}

	public function verifica_tabela($tabela,$sql_array = array(),$cria_tabela = false)
	{
		if($this->execulta("SHOW TABLES LIKE '".$tabela."'")->rowCount() > 0)
		{
			return true;
		}
		else		
		{
			if($cria_tabela)
			{
				foreach ($sql_array as $sql){
					$this->cria_tebela($sql);
				}
				return true;
			}
			else
			{
				return false;
			}	
		}	
	}

	public function tipo_campos($campo,$tipo)
	{
		switch($tipo) 
		{
			case 0:
				//inteiro, id
				return "`".$campo."` int(11) DEFAULT NULL,";
			break;
			case 1:
				//titulo,palavra,string
				return "`".$campo."` varchar(255) DEFAULT NULL,";
			break;
			case 2:
				//textos
				return "`".$campo."` text,";
			break;
			case 3:
				//data e time
				return "`".$campo."` timestamp NULL DEFAULT CURRENT_TIMESTAMP,";
			break;
			case 4:
				//preco
				return "`".$campo."` decimal(15,4) NOT NULL DEFAULT '0.0000',";
			break;
			default:
				return "`".$campo."` varchar(255) DEFAULT NULL,";
			break;
		}
	}

	public function cria_tebela($ar_tab)
	{
			
			///EXEMPLO///
			//$ar_tab = array('tabela'=>array('data'=>3,'titulo'=>1,'texto'=>2));
			/////////////

			foreach ($ar_tab as $tab => $n) 
			{
				$string = "CREATE TABLE IF NOT EXISTS `".$tab."` (`ID` int(11) NOT NULL AUTO_INCREMENT, ";
				foreach ($n as $c => $v) 
				{
					$string .= $this->tipo_campos($c,$v);
				}	
				$string .= "PRIMARY KEY (`ID`)";
				$string .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8";
			}	
			return $this->execulta($string);
	}


}


//$a = new Conexao();


#INSERIR
/*echo $a->inserir('usuarios',array("user"=>"juao@juao.com","sernha"=>"cmd1234"));*/

#SELECIONA
/*foreach ($a->seleciona('usuarios',null,null) as  $value) 
{
	print_r($value->user);
}*/


#ATUALIZA
/*echo $a->atualiza('usuarios',array("user"=>"jjj","sernha"=>"555"),array("ID"=>1));*/

#APAGA
/*echo $a->apaga('usuarios',array("ID"=>25));*/




?>
