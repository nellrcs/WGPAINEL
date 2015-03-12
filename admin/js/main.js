$(document).ready(function (e) {

		var situacao = false;
		var img = document.createElement("img");
		var larg = $("#largura").text();
		var altu = $("#altura").text();
		var tamanho = $("#tamanho").text();
		var prev_img = 250;
		var resoluca_rel = (prev_img * parseInt(altu)) / parseInt(larg);

		$('#image_preview').css("display", "block");
		$('#image_preview').css("height", parseInt(resoluca_rel));

		if($('#previewing').attr('src') == "")
		{
			$("#message").html("<div class='alert alert-dismissable alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>O campo de imagem esta vazio</div>");
		}
		else if($('#previewing').width() < larg)
		{
			var nrel = ( parseInt($('#previewing').width()) * prev_img ) / larg;
			$('#previewing').attr('width', nrel);
			$("#message").html("<div class='alert alert-dismissable alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>A proporcao da imagem e menor que a recomendada !</div>");
		}	
		else
		{
			$('#previewing').attr('width', prev_img);
			$("#message").html("<div class='alert alert-dismissable alert-info'><button type='button' class='close' data-dismiss='alert'>×</button> Quanto mais proximo a imagem ficar das dimensoes recomedadas melhor sera a apresentacao no site!</div>");
		}	

		$('#regua').css({'outline':"2px dashed #666","position":"absolute","width":prev_img,"height":parseInt(resoluca_rel)});

		$(function() {
			$("#foto").change(function() {
				$("#message").empty();		
				var imagem = this.files[0];
				var tipos = imagem.type;
				var arr= ["image/jpeg","image/png","image/jpg"];

				if(!((tipos==arr[0]) || (tipos==arr[1]) || (tipos==arr[2])))
				{
					$('#previewing').attr('src','http://placehold.it/250x250');
					$("#message").html("<div class='alert alert-dismissable alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button> :(  Nao eh permitido enviar arquivos neste formato !</div>");
					return false;

				}
				else if(imagem.size > (tamanho * 1024) )
				{
					$('#previewing').attr('src','http://placehold.it/250x250');
					$("#message").html("<div class='alert alert-dismissable alert-danger'><button type='button' class='close' data-dismiss='alert'>×</button> :( O tamnaho do arquivo eh maior do que o recomendado!</div>");
					return false
				}
				else
				{	
					var reader = new FileReader();
					reader.onload = imageIsLoaded;
					reader.readAsDataURL(this.files[0]);
				}
			});
		});

		function imageIsLoaded(e) {
				$("#foto").css("color","green");
				$('#previewing').attr('src', e.target.result);
				img.src = e.target.result;
				if(img.width < larg)
				{
					var nrel = ( parseInt(img.width) * prev_img ) / larg;
					$('#previewing').attr('width', nrel);
					$("#message").html("<div class='alert alert-dismissable alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>A proporcao da imagem e menor que a recomendada !</div>");
				}	
				else
				{
					$('#previewing').attr('width', prev_img);
					$("#message").html("<div class='alert alert-dismissable alert-info'><button type='button' class='close' data-dismiss='alert'>×</button>Quanto mais proximo a imagem ficar das dimensoes recomedadas melhor sera a apresentacao no site!</div>");
				}	
			};
});


function enviado(data)
{
	console.log(data);
}

/* error */
function erro(data)
{
	console.log(data);
}
/*  beforeSend */
function antes_enviar(data)
{
	console.log(data);
} 
/* complete */
function completo(data)
{
	console.log(data);	
}

function apagar(tabela,where,tembem)
{
	
	if(confirm("Deseja realmente apagar este item?"))
	{	
		$.ajax({
			url: 'io/apagar.php',
			type: "POST",            
			data:  "tambem="+tembem+"&tabela="+tabela+"&where="+where,
			success: function(data)
			{
				alert(data)
				location.reload();
			}
		});

	}
}



