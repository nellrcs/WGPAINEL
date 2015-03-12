<?php 
	$banco =$base->seleciona('sistema',array("ID"=>1),array('redes_sociais'));
	$obj = isset($banco[0]) ? $banco[0]->redes_sociais : new stdClass() ;
	$campos = json_decode(stripslashes($obj));
?>


	<div id="rso">
			<h1>Redes sociais</h1>
			<br>
			<div class="row">

				<div class="col-lg-6">
					<div class="input-group">
					  <span class="input-group-addon" id="basic-addon1"><img src="img/ico_facebook.png" alt=""></span>
					  <input type="text" name="facebook" class="form-control" value="<?= $campos->facebook; ?>" aria-describedby="basic-addon1">
					</div>
					<br>
				</div>				
	
				<div class="col-lg-6">
					<div class="input-group">
					  <span class="input-group-addon" id="basic-addon1"><img src="img/ico_twitter.png" alt=""></span>
					  <input type="text" name="twitter" class="form-control" value="<?= $campos->twitter; ?>" aria-describedby="basic-addon1">
					</div>
					<br>
				</div>				
	
				<div class="col-lg-6">
					<div class="input-group">
					  <span class="input-group-addon" id="basic-addon1"><img src="img/ico_linkedin.png" alt=""></span>
					  <input type="text" name="linkedin" class="form-control" value="<?= $campos->linkedin; ?>" aria-describedby="basic-addon1">
					</div>
					<br>
				</div>				
				
				<div class="col-lg-6">
					<div class="input-group">
					  <span class="input-group-addon" id="basic-addon1"><img src="img/ico_google_plus.png" alt=""></span>
					  <input type="text" name="google_plus" class="form-control" value="<?= $campos->google_plus; ?>" aria-describedby="basic-addon1">
					</div>
					<br>
				</div>				
	
				<div class="col-lg-6">
					<div class="input-group">
					  <span class="input-group-addon" id="basic-addon1"><img src="img/ico_youtube.png" alt=""></span>
					  <input type="text" name="youtube" class="form-control" value="<?= $campos->youtube; ?>" aria-describedby="basic-addon1">
					</div>
					<br>
				</div>				
	
				<div class="col-lg-6">
					<div class="input-group">
					  <span class="input-group-addon" id="basic-addon1"><img src="img/ico_reddit.png" alt=""></span>
					  <input type="text" name="reddit" class="form-control" value="<?= $campos->reddit; ?>" aria-describedby="basic-addon1">
					</div>
					<br>
				</div>

		</div>

	</div>

	<form action="io/update.php" id="redes_soc" method="POST">
		<input name="tabela" type="hidden" value="<?= base64_encode('sistema'); ?>">
		<input name="where" type="hidden" value="<?= base64_encode(json_encode(array("ID"=>"1"))); ?>">
		<input name="redes_sociais" type="hidden" id="para_o_banco" >
		<br>
		<button type="submit" class="btn btn-primary"> SALVAR </button>
		<br>
		<br>
	</form>




<script>
	var campos = $("#rso");	
    function modifica()
    {
         var textoX = {};
         $.each( campos.find("input"), function( key, valor ) 
         {      
  			textoX[valor.name] = valor.value;	
         });

         $("#para_o_banco").val(JSON.stringify(textoX));
    }

    campos.find('input').on("keyup",function()
    {  
      modifica();
    });

    campos.click(function () 
    { 
      modifica();
    });

    modifica();
	$("#redes_soc").on('submit',function(e)
	{
		e.preventDefault();
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
