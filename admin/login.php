<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">
    <title>Painel adminstrativo</title>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap3.min.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">
    <link href="css/wow.css" rel="stylesheet">
    <script src="js/jquery.min.js"></script>
    <script src="js/wow.min.js"></script>
  </head>
<body>   


<div class="site-wrapper">

      <div class="site-wrapper-inner">

        <div class="cover-container">

          <div class="inner cover">
            
            <div class="panel panel-default box-adm wow bounceInUp center animated">
              
              <div class="panel-heading">
                <br>
                <h1>ADMINISTRADOR</h1>
              </div>
              <div class="panel-body">
                  <br>
                  <form id="login" method="POST" action="io/permissao.php">
                    <label for="usuario" class="pull-left">Usuario:</label>
                    <input type="text" id="usuario" name="usuario" placeholder="Usuario" class="form-control">
                    <br>
                    <label for="senha" class="pull-left">Senha:</label>
                    <input type="password" id="senha" name="senha" placeholder="Senha" class="form-control">
                    <br>
                    <div class="result">
                    </div>
                    <br>
                    <input type="submit" name="enviar" class="btn-lg btn-primary " value="Entrar">
                    <br>
                  </form>
                </div>

          </div>
        </div>


          <div class="mastfoot">
            <div class="inner">
              <!-- <p>@warllencs.</p> -->
            </div>
          </div>

        </div>

      </div>

    </div>
    
<script type="text/javascript">
new WOW().init();
$("#login").submit(function(e)
{
    var postData = $(this).serializeArray();
    var formURL = $(this).attr("action");
    var msg = "";
    $.ajax(
    {
        url : formURL,
        type: "POST",
        data : postData,
        dataType: "json",
        beforeSend: function() {
          $(".result").html("<pre class='alert'><img src='../img/AjaxLoader.gif' id='load' > Aguarde...</pre>");
        },
        complete: function(){
         $(".result").html("<pre class='alert' style='color: #333;'>"+msg+"</pre>");
        },
        success:function(data) 
        {           
          if(data.status == true)
          {
                window.location = data.url;   
          }
          else
          {
            msg = "Login ou senha estão incorretos.";
          } 

        },
        error: function() 
        {
          msg = "Login ou senha estão incorretos.";
        }
    });
    e.preventDefault();
    e.unbind();
});
 </script>       
        
        
</body>
</html>