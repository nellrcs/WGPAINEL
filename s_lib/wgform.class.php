<?php 
/**
* Objetivo unificar a criacao de formularios.
* author @warllencs
* ver 1.0.1
*/

class obj_data
{
	public $campo = null;
	public $titulo = '';
	public $valor = null;
	public $tipo = 0;
	public $css_class = 'form-control';
	public $opcoes = null;
	public $placeholder = true;
	public $id = null;
	public $obrigatorio = false;
	public $label = false;
	public $html_antes = null;
	public $html_depois = null;
	public $mascara = '';
}

class WGform 
{

	public static $nome = null;
	public static $action = null;
	public static $css_class = null;
	public static $html_botao =  null;
	public static $method = 'POST';
	public static $prefixo = 'WG';
	public static $ajax = false;
	public static $campos_mascara = array();

	function index($valor = array())
	{
		$verdadeiro = new obj_data();	

		if(!empty($valor)):
			foreach ($valor as $chave => $valor){
				if(property_exists($verdadeiro,$chave)):
					$verdadeiro->$chave = $valor;  	
				endif;	
			}
			if(empty($verdadeiro->campo)):
				$verdadeiro->campo = self::$prefixo.uniqid();	
			endif;

			if(empty($verdadeiro->titulo)):
				$verdadeiro->titulo = $verdadeiro->campo;
			endif;

			if(empty($verdadeiro->id)):
				$verdadeiro->id = $verdadeiro->campo;
			endif;

			if(!empty($verdadeiro->mascara)):
				self::$campos_mascara[$verdadeiro->id] = $verdadeiro->mascara;
			endif;	

			return $verdadeiro;
		else:
			return false;
		endif;	
		
	}

	public static function formulario($obj)
	{
		$html = "";
		foreach ($obj as $c => $v)
		{
			if(!empty($v)):
				$html .= self::add($v->tipo,$v);
			endif;		
		}

		if(!empty(self::$nome)):
			$form = "<form";
			$form .= " action='".self::$action."'";
			$form .= " method='".self::$method."'";
			$form .= " class='".self::$css_class."'";
			$form .= " id='".self::$nome."'";
			$form .= ' enctype="multipart/form-data">';
			$form .= $html;
			$form .= self::$html_botao ? self::$html_botao : "<input type='submit'>";
			$form .= "</form>";
			$html = $form;
		endif;	

		$ar_scripts = array();

		if(!empty(self::$campos_mascara)):
			$ar_scripts[] = self::mascara(self::$campos_mascara);
		endif;

		if(!empty(self::$ajax)):
			$ar_scripts[] = self::ajax(self::$nome);
		endif;

		if(!empty($ar_scripts)):
			$html .= self::scripts($ar_scripts);
		endif;	

		return $html; 
	}

	public static function label($nome,$for)
	{
		$label = '<label for="'.$for.'" >'.$nome.'</label> ';
		return $label;
	}

	public static function input($valores_campo,$type = null)
	{
		/* INPUT */
		$html = $valores_campo->html_antes ? $valores_campo->html_antes: '';
		$html .= '<input ';
		if($type):
			$html .= 'type="'.$type.'" ';
		else:	
			$html .= 'type="text" ';
		endif;
		$html .= $valores_campo->campo ? 'name="'.$valores_campo->campo.'" ' : '';
		$html .= $valores_campo->css_class ? 'class="'.$valores_campo->css_class.'" ' : '';
		$html .= $valores_campo->id ? 'id="'.$valores_campo->id.'" ' : '';
		$html .= $valores_campo->valor ? 'value="'.$valores_campo->valor.'" ' : '';
		$html .= $valores_campo->obrigatorio ?  'required ' : '';
		if($valores_campo->placeholder):
			$html .=  $valores_campo->titulo ? ' placeholder="'.$valores_campo->titulo.'" ' : '';	
		endif;

		$html .= '/>';
		if($valores_campo->label):
			$temp = $html;
			$html = self::label($valores_campo->titulo,$valores_campo->id).$temp;	
		endif;	
		$html .= $valores_campo->html_depois ? $valores_campo->html_depois: '';		
		return $html;
	}

	public static function textarea($valores_campo)
	{
		/* TEXTAREA */
		$html = $valores_campo->html_antes ? $valores_campo->html_antes: '';
		$html .= '<textarea ';
		$html .= $valores_campo->css_class ? 'class="'.$valores_campo->css_class.'" ' : '';
		$html .= $valores_campo->id ? 'id="'.$valores_campo->id.'" ' : '';
		$html .= $valores_campo->campo ? 'name="'.$valores_campo->campo.'" ' : '';
		if($valores_campo->placeholder):
			$html .=  $valores_campo->titulo ? ' placeholder="'.$valores_campo->titulo.'" ' : '';	
		endif;
		$html .= '>';
		$html .= $valores_campo->valor ?  $valores_campo->valor : '';
		$html .= '</textarea> ';
		if($valores_campo->label):
			$temp = $html;
			$html = self::label($valores_campo->titulo,$valores_campo->id).$temp;
		endif;
		$html .= $valores_campo->html_depois ? $valores_campo->html_depois: '';			
		return $html;
	}

	public static function select($valores_campo)
	{
		/* SELECT */
		$html = $valores_campo->html_antes ? $valores_campo->html_antes: '';
		$html .= '<select ';
		$html .= $valores_campo->css_class ? 'class="'.$valores_campo->css_class.'" ' : '';
		$html .= $valores_campo->id ? 'id="'.$valores_campo->id.'" ' : '';
		$html .= $valores_campo->campo ? 'name="'.$valores_campo->campo.'" ' : '';
		$html .= '>';
		if(is_array($valores_campo->opcoes) || is_object($valores_campo->opcoes)):
			foreach ($valores_campo->opcoes as $c => $v) {
				if($c[0] == '@'):
					$b = str_replace("@", "", $c);
					$html .= '<option value="'.$b.'" selected="selected">'.$v.'</option> ';
				else:
					$html .= '<option value="'.$c.'">'.$v.'</option> ';
				endif;
			}	
		endif;
		$html .= '</select> ';	
		if($valores_campo->label):
			$temp = $html;
			$html = self::label($valores_campo->titulo,$valores_campo->id).$temp;
		endif;
		$html .= $valores_campo->html_depois ? $valores_campo->html_depois: '';						
		return $html;
	}

	public static function radio($valores_campo)
	{
		/* RADIO */
		$html = $valores_campo->html_antes ? $valores_campo->html_antes: '';	
		$html .= '<input ';
		if($valores_campo->campo == '@'):
			$b = str_replace("@", "", $valores_campo->campo);
			$valores_campo->campo = $b;
			$html .= 'checked="1"';
		endif;
		$html .= 'type="radio" ';
		$html .= $valores_campo->campo ? 'name="'.$valores_campo->campo.'" ' : '';
		$html .= $valores_campo->id ? 'id="'.$valores_campo->id.'" ' : '';
		$html .= $valores_campo->valor ? 'value="'.$valores_campo->valor.'" ' : '';
		$html .= ">";

		if($valores_campo->label):
			$temp = $html;
			$html = self::label($valores_campo->titulo,$valores_campo->id).$temp;
		endif;	
		$html .= $valores_campo->html_depois ? $valores_campo->html_depois: '';	
		return $html;
	}

	public static function checkbox($valores_campo)
	{
		/* CHECKBOX */
		$html = $valores_campo->html_antes ? $valores_campo->html_antes: '';
		$html .= '<input ';
		$html .= 'type="checkbox"';
		$html .= $valores_campo->campo ? 'name="'.$valores_campo->campo.'" ' : '';
		$html .= $valores_campo->id ? 'id="'.$valores_campo->id.'" ' : '';
		$html .= $valores_campo->valor ? 'value="'.$valores_campo->valor.'" ' : '';
		$html .= ">";
		if($valores_campo->label):
			$temp = $html;
			$html = self::label($valores_campo->titulo,$valores_campo->id).$temp;
		endif;	
		$html .= $valores_campo->html_depois ? $valores_campo->html_depois: '';	
		return $html;
	}

	public static function oculto($valores_campo)
	{
		/* OCULTO */
		$html = '<input ';
		$html .= 'type="hidden" ';
		$html .= $valores_campo->campo ? 'name="'.$valores_campo->campo.'" ' : '';
		$html .= $valores_campo->id ? 'id="'.$valores_campo->id.'" ' : '';
		$html .= $valores_campo->valor ? 'value="'.$valores_campo->valor.'" ' : '';
		$html .= '/>';	
		return $html;
	}

	public static function arquivo($valores_campo)
	{
		/* ARQUIVO */
		$html = $valores_campo->html_antes ? $valores_campo->html_antes: '';
		$html .= '<input ';
		$html .= $valores_campo->campo ? 'name="'.$valores_campo->campo.'" ' : '';
		$html .= $valores_campo->id ? 'id="'.$valores_campo->id.'" ' : '';
		$html .= $valores_campo->valor ? 'value="'.$valores_campo->valor.'" ' : '';
		$html .= 'type="file"> ';	
		if($valores_campo->label):
			$temp = $html;
			$html = self::label($valores_campo->titulo,$valores_campo->id).$temp;
		endif;	
		$html .= $valores_campo->html_depois ? $valores_campo->html_depois: '';	
		return $html;
	}	




	public static function scripts($trechos = array())
	{
		$html = '<script type="text/javascript">';
		foreach ($trechos as $trecho) {
			$html .= $trecho; 
		}
		$html .= '</script>';
		return $html;
	}
	
	public static function mascara($id_campos)
	{
		$html = "";
		foreach($id_campos as $c => $v)
		{
			$html .= '$("#'.$c.'").mask("'.$v.'");';
		}
		return $html;
	}

	public static function ajax($id_formaulario)
	{
		$eventos = "";
		if(is_array(self::$ajax)):
			$eventos .= !empty(self::$ajax[0]) ? "success:function(t){".self::$ajax[0]."(t)}" : "success:function(t){}";
			$eventos .= !empty(self::$ajax[1]) ? ",error:function(t){".self::$ajax[1]."(t)}" : ",error:function(t){}";
			$eventos .= !empty(self::$ajax[2]) ? ",beforeSend:function(t){".self::$ajax[2]."(t)}" : ",beforeSend:function(t){}";
			$eventos .= !empty(self::$ajax[3]) ? ",complete:function(t){".self::$ajax[3]."(t)}" : ",complete:function(t){}";
		else:
			$eventos = 'success:function(t){alert("enviado")}';
		endif;


		if(!empty($id_formaulario)):
			$id_form = "#".$id_formaulario;
			$html = '$(document).ready(function(){$("'.$id_form.'").on("submit",function(t){t.preventDefault();var a=$(this).attr("action"),e=$(this).attr("method");$.ajax({url:a,type:e,data:new FormData(this),contentType:!1,cache:!1,processData:!1,'.$eventos.'})})});';
			return $html;
		else:		
			return false;
		endif;

	}

	public static function add($tipo,$obj)
	{
		switch ($tipo) {
			case '0':
				return self::input($obj);
			break;
			
			case '1':
				return self::textarea($obj);
			break;

			case '2':
				return self::select($obj);
			break;			

			case '3':
				return self::radio($obj);
			break;			

			case '4':
				return self::checkbox($obj);
			break;

			case '5':
				return self::oculto($obj);
			break;

			case '6':
				return self::arquivo($obj);
			break;

			case '7':
				return self::input($obj,'password');
			break;

			default:
				return self::input($obj);
			break;
		}
	}

}
?>