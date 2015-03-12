<?php 
  $banco =$base->seleciona('sistema',array("ID"=>1),array('palavras_chave','cod_google','API_key','site_ids'));
  $obj = isset($banco[0]) ? $banco[0] : new stdClass() ;
?>

<style>
.bootstrap-tagsinput{background-color:#fff;border:1px solid #ccc;box-shadow:inset 0 1px 1px rgba(0,0,0,.075);display:inline-block;padding:4px 6px;margin-bottom:10px;color:#555;vertical-align:middle;border-radius:4px;max-width:100%;line-height:22px;cursor:text}.bootstrap-tagsinput input{border:none;box-shadow:none;outline:0;background-color:transparent;padding:0;margin:0;width:auto!important;max-width:inherit}.bootstrap-tagsinput input:focus{border:none;box-shadow:none}.bootstrap-tagsinput .tag{margin-right:2px;color:#fff}.bootstrap-tagsinput .tag [data-role=remove]{margin-left:8px;cursor:pointer}.bootstrap-tagsinput .tag [data-role=remove]:after{content:"x";padding:0 2px}.bootstrap-tagsinput .tag [data-role=remove]:hover{box-shadow:inset 0 1px 0 rgba(255,255,255,.2),0 1px 2px rgba(0,0,0,.05)}.bootstrap-tagsinput .tag [data-role=remove]:hover:active{box-shadow:inset 0 3px 5px rgba(0,0,0,.125)}
</style>

<h1>SEO</h1>

<h3>Palavras chave.</h3>
<input type="text" value="<?= $obj->palavras_chave; ?>" class="form-control" id="pc" data-role="tagsinput" >
<p>Palavras-chave são as palavras (ou um grupo delas) que descrevem o tema de um site ou o assunto de um texto, e são usadas pelas ferramentas de busca com o propósito de apresentar resultados relevantes e precisos.</p>

<h2>Codigo google analytics</h2>
<form action="io/update.php" id="salvaPC" method="POST">
	<input name="tabela" type="hidden" value="<?= base64_encode('sistema'); ?>">
	<input name="where" type="hidden" value="<?= base64_encode(json_encode(array("ID"=>"1"))); ?>">
	<input name="palavras_chave" type="hidden" id="plC" >
	<br>
  	<textarea name="cod_google" id="" cols="30" rows="10" class="form-control"><?= stripslashes($obj->cod_google); ?></textarea>
  	<br>
	<h2>APIs do google</h2>
	<div class="Banner-auth" id="auth"></div>
	<br>
	<label for="">ID DO CLIENTE <a href="https://code.google.com/apis/console/" target="new">link</a></label>	
	<input type="text" name="API_key" id="api_key" class="form-control" value="<?= $obj->API_key; ?>">
	<br>
	<div id="view-selector"></div>
	<br>
	<label for="">SITE IDS</label>
	<input type="text" class="form-control" name="site_ids" value="<?= $obj->site_ids; ?>" id="site_ids">
	<br>
	<button type="submit" class="btn btn-primary"> SALVAR </button>
	<br>
	<br>
</form>


<script>
  (function(w,d,s,g,js,fjs){
    g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(cb){this.q.push(cb)}};
    js=d.createElement(s);fjs=d.getElementsByTagName(s)[0];
    js.src='https://apis.google.com/js/platform.js';
    fjs.parentNode.insertBefore(js,fjs);js.onload=function(){g.load('analytics')};
  }(window,document,'script'));
</script>

<script type="text/javascript">
gapi.analytics.ready(function()
{

	var codigo = $('#api_key').val();
	var autorizacao = { container: 'auth',clientid: codigo};
	gapi.analytics.auth.authorize(autorizacao);
	gapi.analytics.auth.on('success', function()
	{
		document.documentElement.classList.add('is-authorized');
	});

	var opcao = new gapi.analytics.ViewSelector({
	  container: 'view-selector'
	});

	opcao.on('change', function(ids) {
	  $('#site_ids').val(ids);
	});
	opcao.execute();

});
</script>























<script>	
$("#salvaPC").on('submit',function(e)
{
	e.preventDefault();
	$('#plC').val($('#pc').val());
	var formURL = $(this).attr("action");
	$.ajax({
		url: formURL,
		type: "POST",            
		data: new FormData(this),
		contentType: false,       
		cache: false,           
		processData:false,        
		success: function(data)
		{
			alert(data)
		}
	})
});
</script>

