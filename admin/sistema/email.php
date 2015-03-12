
<?php 

	$banco =$base->seleciona('sistema',array("ID"=>1),array('config_email'));
	$obj = isset($banco[0]) ? $banco[0]->config_email : new stdClass();
	$campos = json_decode(stripslashes($obj));
?>


	<div id="emailX">
			<h1>Propriedades email</h1>
			<div class="row">
				<div class="col-lg-6">
					<label for="">Host</label>
					<input type="text" name="Host" placeholder="texto" class="form-control" value="<?= $campos->Host; ?>">
				</div>

			<div class="col-lg-6">
					<label for="">Username</label>
					<input type="text" name="Username" placeholder="texto" class="form-control" value="<?= $campos->Username; ?>">
			</div>		
	

			<div class="col-lg-6">
					<label for="">Password</label>
					<input type="text" name="Password" placeholder="texto" class="form-control" value="<?= $campos->Password; ?>">
		
			</div>

			<div class="col-lg-6">
					<label for="">SMTPSecure</label>
					<input type="text" name="SMTPSecure" placeholder="texto" class="form-control" value="<?= $campos->SMTPSecure; ?>">
		
			</div>

			<div class="col-lg-6">
					<label for="">Porta</label>
					<input type="text" name="Port" placeholder="texto" class="form-control" value="<?= $campos->Port; ?>">
		
			</div>

			<div class="col-lg-6">
					<label for="">conta de email que sera usada no envio</label>
					<input type="text" name="From" placeholder="texto" class="form-control" value="<?= $campos->From; ?>">
	
			</div>

			<div class="col-lg-6">
					<label for="">Nome da conta</label>
					<input type="text" name="FromName" placeholder="texto" class="form-control" value="<?= $campos->FromName; ?>">
		
			</div>

			<div class="col-lg-6">
					<label for="">Destinatario | Nome</label>
					<input type="text" name="addAddress" placeholder="texto" class="form-control" value="<?= $campos->addAddress ?>">
			</div>


		</div>

	</div>

	<form action="io/update.php" id="salvaconfiemail" method="POST">
		<input name="tabela" type="hidden" value="<?= base64_encode('sistema'); ?>">
		<input name="where" type="hidden" value="<?= base64_encode(json_encode(array("ID"=>"1"))); ?>">
		<input name="config_email" type="hidden" id="para_o_banco" >
		<br>
		<button type="submit" class="btn btn-primary"> SALVAR </button>
		<br>
		<br>
	</form>

	<script>
	var campos = $("#emailX");	
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
	$("#salvaconfiemail").on('submit',function(e)
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




	