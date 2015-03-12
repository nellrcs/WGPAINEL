<?php 

$lista_de_paginas_site = array('home','empresa','servicos','clientes','portifolio','artigos','contato');
define('DEFNOMESPG',serialize($lista_de_paginas_site));
include "s_php/controler.php";

?>