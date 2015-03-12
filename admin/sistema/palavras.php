<?php 
$base = new Conexao(); 
if(file_exists('../s_php/linguagem/textos.php')):
    include "../s_php/linguagem/textos.php";
endif;

?>

<div class="row">
    <h1>Palavras</h1>
</div>
<?php $total_idiomas = $base->seleciona('idiomas',array('status'=>'1')); ?>
<?php if($total_idiomas): ?>
    <div class="row">
        <pre class="col-lg-4"><?php
            foreach ($base->seleciona('idiomas',array('status'=>'1'),null) as  $val):
                echo "<a href='".adiciona_ao_get(array('lang'=>$val->sigla))."' class='nacoes ".$val->sigla."' title='".$val->nome."' ></a>";
            endforeach;
        ?><a href="<?= adiciona_ao_get(array('lang'=>'br'),array('lang'=>'')) ?>" class="nacoes br" title='Portugues' ></a></pre>
    </div>
<?php endif; ?>

<?php
$js = "";
if(!empty($_GET['lang'])):
    $get_lang = addslashes($_GET['lang']);
    $js = json_encode(array("sigla_idioma"=>$get_lang));
else:
    $get_lang = false;
endif;    
if($get_lang):
    $disable = "";
    $db_trad = $base->seleciona('traducao',array("sigla_idioma"=>$get_lang),null);
    
    if($db_trad == true):
        $dx = $db_trad[0];
        $palavas2 = json_decode(stripslashes($dx->json),true);
    else:
        $base->inserir('traducao',array("sigla_idioma"=> $get_lang ,"json"=>json_encode($palavas) ) );
        $palavas2 = $palavas;
    endif; 
else:
    $palavas2 = $palavas;
    $disable = "disabled";
endif;

?> 
<br>
<br>        
<ul id="traducao" class="row">
<?php 
foreach ($palavas as $ide => $n):
    echo "<li id='".$ide."' class='col-lg-6'><pre class='alert alert-dismissable alert-success' style='cursor: pointer;' id='".strtolower($ide)."' >".$ide."</pre><br>";
    echo "<span data-togle='".strtolower($ide)."'>";
	foreach ($n as $chave => $valor):
        echo "<label>".$valor.":</label>";
        if(!empty($palavas2[$ide][$chave])):
            echo "<input type='text'   class='form-control' id='".$chave."' ".$disable." value='".$palavas2[$ide][$chave]."'></br>";    
        else:   
		     echo "<input type='text' class='form-control' id='".$chave."' ".$disable." value='".$valor."'></br>";
        endif;		       
	endforeach;
	echo "</li>";
    echo "</span>"; 
endforeach;
?>
</ul>
    <form action="io/update.php" class="row" id="das">
        <input type="hidden" id="dados" name="json" >
        <input type="hidden" name="tabela" value="<?= base64_encode("traducao"); ?>">
        <br>
        <?php if($get_lang): ?>
        <input type="hidden" name="where" value='<?=  base64_encode($js); ?>'> 
        <button type="submit" class="btn btn-default">Salvar</button>
        <?php endif; ?>
    </form>
<script>
    var campos = $("#traducao");
    function modifica()
    {
         var textoX = {};
         $.each( campos.find("li"), function( key, value ) {      
            offArraY = {};
            $.each( campos.find("li[id="+value.id+"]").find('input'), function( k, v ) {
                offArraY[v.id] = v.value;
            }) 
            textoX[value.id] = offArraY;
         });
         $("#dados").val(JSON.stringify(textoX));
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

$("#das").submit(function(e)
{
   
    var postData = $(this).serializeArray();
    var formURL = $(this).attr("action");

    $.ajax(
    {
        url : formURL,
        type: "POST",
        data : postData,
        beforeSend: function() {
          
        },
        complete: function(){
          
        },
        success:function(data) 
        {
            alert(data);
        },
        error: function() 
        {

        }
    });
    e.preventDefault();
    e.unbind();
});


$("#traducao li pre").on('click',function(e){

    //$(this).attr('id') );
    $("span[data-togle='"+$(this).attr('id')+"']").toggle();
});


</script>

 