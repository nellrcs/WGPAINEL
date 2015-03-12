<?php 
$base = new Conexao(); 
?>

  <h1>Idiomas</h1>
  <form action="io/update.php" method="POST"  id="form_cotacao" class="form-horizontal">
	<?php foreach ($base->seleciona('idiomas',null,null) as  $val):  ?>
	<div class="form-group">
        <label class="col-lg-2 control-label"><span class="nacoes <?= $val->sigla ?> pull-left"></span><?= $val->nome; ?></label>
        <div class="col-lg-10">
          <div class="radio">
            <label>
              <input type="radio" name="<?=  $val->sigla; ?>" id="" class="botao" value="1" <?php if($val->status == 1){echo " checked"; } ?> >
              Sim
            </label>
            <label>
              <input type="radio" name="<?=  $val->sigla; ?>" id="" class="botao" value="0"  <?php if($val->status == 0){echo " checked"; } ?> >
              Nao
            </label>
          </div>
        </div>
     </div>
	<?php  endforeach; ?>
	<br>
</form>
<script>	
$( ".botao" ).change(function() { 
  var where = {'sigla':$(this).attr('name')}
  var tabela = 'idiomas';
  var status = $(this).val();
  muda_status_idioma(btoa(tabela),btoa(JSON.stringify(where)),status);
});

function muda_status_idioma(tab,whe,sts)
{
	var uRl = "tabela="+tab+"&status="+sts+"&where="+whe;
	$.ajax({
		url: 'io/update.php',
		type: "POST",            
		data: uRl,    
		success: function(data)
		{
			//alert(data)
		}
	});

}

</script>