<?php 
include "config.php";
include "s_lib/UrlAmigavel.class.php";
include "s_lib/conexao.class.php";
include "s_lib/funcoes_basicas.php";
require_once("s_lib/raelgc/view/Template.php");
include "s_php/linguagem/textos.php";

define('URL_SITE', UrlAmigavel::url_padrao('http'));
$tpl = new Template("s_html/index.html");
$base = new Conexao();
$lingua = "";

#DEFINE O CAMIHO DE URL PADRAO img,css,href etc {BASE}
if($tpl->exists('BASE')) $tpl->BASE = URL_SITE;

#VERIFICA SE AS TEBELAS EXISTEM
$tabelas = array('idiomas','sistema','usuarios','textos');

$verif_banco = true;
foreach ($tabelas as $valor):
  if($base->verifica_tabela($valor) == false) $verif_banco = false;
endforeach;

if($verif_banco == false) die(header("Location: ".URL_SITE."instalar/"));

#VERIFICA SE O IDIOMA EXISTE
if(UrlAmigavel::campo_url()):
    $lg = $base->seleciona('idiomas',array('status' =>"1"));
    $db_idioma = array();
    foreach ($lg as $v):
        $db_idioma[] = $v->sigla; 
    endforeach;
    if(in_array(UrlAmigavel::campo_url(0), $db_idioma)):
        $lingua = UrlAmigavel::campo_url(0);
        $url = array_merge(array(),array_diff_key(UrlAmigavel::campo_url(),array(0 => "")));
    else:
         $url = UrlAmigavel::campo_url();  
    endif;   
else:
    $url = UrlAmigavel::campo_url();
endif; 

$html_lang = $base->seleciona('idiomas',array('status' =>"1","sigla"=>$lingua),array('html_lang'));
$html_lang = isset($html_lang[0]->html_lang) ? $html_lang[0]->html_lang : "pt-br";


#COLACA O A SIGLA DO IDIOMA NOS LINKS {LG} 
if($lingua != ""):
    if($tpl->exists("LG")){ $tpl->LG = $lingua."/";}
    $db_trad = $base->seleciona('traducao',array("sigla_idioma"=>$lingua),null);    
    if($db_trad[0]):
        $dx = $db_trad[0];
        $palavas = json_decode(stripslashes($dx->json),true);
    endif;
endif;

#INCLUI AS PAGINA
/* NO o inlucude sera feito no {INCLUIR_PAGINA} do html*/
if(!empty($url[0])):
    if(in_array($url[0], unserialize(DEFNOMESPG) )):
      if(file_exists("s_html/".$url[0].".html")):
        $tpl->addFile("INCLUIR_PAGINA", "s_html/".$url[0].".html");
      endif;
      if(file_exists("s_php/paginas/".$url[0].".php")):
        include("s_php/paginas/".$url[0].".php");
      endif;
    else:
      if(file_exists("s_html/home.html")):
        $tpl->addFile("INCLUIR_PAGINA", "s_html/home.html");
      endif;
      if(file_exists("s_php/paginas/home.php")):
        include("s_php/paginas/home.php");
      endif;
    endif;
else:
  if(file_exists("s_html/home.html")):
    $tpl->addFile("INCLUIR_PAGINA", "s_html/home.html");
  endif;
  if(file_exists("s_php/paginas/home.php")):
    include("s_php/paginas/home.php");
  endif;
endif;


# PALAVRAS
if(!empty($palavas)):
foreach ($palavas as $v):
	foreach ($v as $chave => $valor):
		if($tpl->exists($chave)) $tpl->$chave = $valor;
    endforeach;
endforeach;
endif;

$sistema = $base->seleciona('sistema',array('ID' =>"1"));
$sis = isset($sistema[0]) ? $sistema[0] : "";

$email = json_decode(stripslashes($sis->config_email));
$email = isset($email->addAddress) ? explode("|", $email->addAddress) : array('1','2');
$social = json_decode(stripslashes($sis->redes_sociais));


# STRINGS PADROES
if($tpl->exists('_HTML_LANG')) $tpl->_HTML_LANG = $html_lang;
if($tpl->exists('UNICO_ID')) $tpl->UNICO_ID = md5(uniqid());
if($tpl->exists('FONE_SITE')) $tpl->FONE_SITE = $sis->telefones;
if($tpl->exists('EMAIL_SITE')) $tpl->EMAIL_SITE = $email[0];
if($tpl->exists('SITE_ENDERECO')) $tpl->SITE_ENDERECO = $sis->endereco;
if($tpl->exists('GOOGLE')) $tpl->GOOGLE = stripslashes($sis->cod_google);


if($tpl->exists('SOCIAL_FACE')) $tpl->SOCIAL_FACE = $social->facebook;
if($tpl->exists('SOCIAL_TWITTER')) $tpl->SOCIAL_TWITTER = $social->twitter;
if($tpl->exists('SOCIAL_GOOGLE')) $tpl->SOCIAL_GOOGLE = $social->google_plus;
if($tpl->exists('SOCIAL_LINKEDIN')) $tpl->SOCIAL_LINKEDIN = $social->linkedin;
if($tpl->exists('SOCIAL_YOUTUBE')) $tpl->SOCIAL_YOUTUBE = $social->youtube;
if($tpl->exists('SOCIAL_REDDIT')) $tpl->SOCIAL_REDDIT = $social->reddit;


#TEXTOS SITE
$tTexto = $base->seleciona('textos',array('tipo' =>"empresa","sigla_idioma"=>$lingua));
$texto = !empty($tTexto[0]) ? $tTexto[0] : false;;

$tpl->show();
    
 ?>