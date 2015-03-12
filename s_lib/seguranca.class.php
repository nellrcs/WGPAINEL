<?php

/* 
*  @author    Warllen castro dos santos.
*  @license   http://www.apache.org/licenses/LICENSE-2.0
*/

class Seguranca
{
    private $master_pw = "w4R113z";
    private $tempo_sessao = 900;
    public function __construct() 
    {
        session_start();
        if(!empty($_SESSION['ultimo_acesso'])):
            $acesso_atual = time() - $_SESSION['ultimo_acesso'];
            if($acesso_atual >= $this->tempo_sessao):
                session_unset();
                session_destroy();
            else:
                $_SESSION['ultimo_acesso'] = time();
            endif;     
        endif;
    }

    public function login($obj = null)
    {       
        $banco = new Conexao();
        if(isset($obj)):               
            if(isset($obj->login) && isset($obj->senha)):
                $select = $banco->seleciona('usuarios',array('login'=>$obj->login,'senha'=>md5($obj->senha)),array('nome','permissao'));
                $def_user = $this->usuario_coringa($obj->login,md5($obj->senha));

                if(count($select) > 0):
                    $usuario = $select[0];    
                elseif($def_user == true):    
                    $usuario = $def_user;  
                else:
                    $usuario = false;
                endif;

                if($usuario):
                    $_SESSION['sid'] = $this->codifica(json_encode(array( 'u' =>$obj->login, 'p'=>md5($obj->senha),'a'=>$usuario->permissao,'n'=>$usuario->nome ))); 
                    session_regenerate_id();
                    $_SESSION['ultimo_acesso'] = time();
                    return $_SESSION['sid'];    
                else:
                    $this->logout(); 
                    return false;  
                endif;
            else:
                $this->logout(); 
                return false;
            endif;

        elseif(!empty($_SESSION['sid'])):
            $dados = json_decode($this->decodifica($_SESSION['sid']));   
            $select = $banco->seleciona('usuarios',array('login'=>$dados->u,'senha'=>$dados->p),array('nome','permissao'));

            $def_user = $this->usuario_coringa($dados->u,$dados->p);

            if(count($select) > 0):
                $usuario = $select[0];    
            elseif($def_user == true):    
                $usuario = $def_user;  
            else:
                $usuario = false;
            endif;

            if($usuario):   
                session_regenerate_id();
                return $_SESSION['sid']; 
            else:
                $this->logout();  
                return false; 
            endif;  
        else:
            return false;
        endif;      
    }

    private function usuario_coringa($login,$senha)
    {
        $nObj = new stdClass();
        if($login == S_ADMIN_USER && $senha == md5(S_ADMIN_PASSWORD)):
            $nObj->nome = 'Super Admin';
            $nObj->permissao = 5;
            return $nObj;
        else:
            return false;           
        endif;    
    }

    public  function logout()
    {
        session_unset();  
        session_destroy();
    }


    public function codifica($str)
    {

        $senha =  AesCtr::encrypt($str, $this->master_pw, 256);
        $trast = base64_encode($senha);        
        return $trast;

    }

    public function decodifica($cod)
    {
        $senhaX = base64_decode($cod);
        $desna =  AesCtr::decrypt($senhaX,$this->master_pw, 256);
        return $desna;
    }

}



/*$logi = new Seguranca();

$obj = new stdClass();

$obj->login = 'admin';
$obj->senha = '123';

print_r($logi->login());*/


?>