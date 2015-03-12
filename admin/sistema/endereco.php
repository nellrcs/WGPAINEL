
<?php 
  $banco =$base->seleciona('sistema',array("ID"=>1),array('endereco','telefones'));
  $obj = isset($banco[0]) ? $banco[0] : new stdClass() ;
?>


<script type='text/javascript' src='https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places'></script>

<style type="text/css">
#map-canvas {height: 400px;width: 100%;margin: 0px;padding: 0px}.noscrollbar {line-height:1.35;overflow:hidden;white-space:nowrap;}
.bootstrap-tagsinput{background-color:#fff;border:1px solid #ccc;box-shadow:inset 0 1px 1px rgba(0,0,0,.075);display:inline-block;padding:4px 6px;margin-bottom:10px;color:#555;vertical-align:middle;border-radius:4px;max-width:100%;line-height:22px;cursor:text}.bootstrap-tagsinput input{border:none;box-shadow:none;outline:0;background-color:transparent;padding:0;margin:0;width:auto!important;max-width:inherit}.bootstrap-tagsinput input:focus{border:none;box-shadow:none}.bootstrap-tagsinput .tag{margin-right:2px;color:#fff}.bootstrap-tagsinput .tag [data-role=remove]{margin-left:8px;cursor:pointer}.bootstrap-tagsinput .tag [data-role=remove]:after{content:"x";padding:0 2px}.bootstrap-tagsinput .tag [data-role=remove]:hover{box-shadow:inset 0 1px 0 rgba(255,255,255,.2),0 1px 2px rgba(0,0,0,.05)}.bootstrap-tagsinput .tag [data-role=remove]:hover:active{box-shadow:inset 0 3px 5px rgba(0,0,0,.125)}
</style>



<script>function initialize(){geocoder=new google.maps.Geocoder;var e=new google.maps.LatLng(-34.397,150.644),o={zoom:12,center:e};map=new google.maps.Map(document.getElementById("map-canvas"),o),selectAddress()}function geocodeIt(e){geocoder.geocode({address:e},function(o,n){n==google.maps.GeocoderStatus.OK&&(map.setCenter(o[0].geometry.location),marker=new google.maps.Marker({map:map,position:o[0].geometry.location,animation:google.maps.Animation.DROP}),addInfoWindow(marker,e))})}function selectAddress(){var e=$("#endereco").val();geocodeIt(e),autocomplete=new google.maps.places.Autocomplete(document.getElementById("endereco"),{types:["geocode"]}),google.maps.event.addListener(autocomplete,"place_changed",function(){e=$("#endereco").val(),marker.setMap(null),geocodeIt(e)})}function drop(){setTimeout(function(){addMarker()},200)}function addInfoWindow(e,o){var n=new google.maps.InfoWindow({content:'<div class="noscrollbar">'+o+'</div>'});google.maps.event.addListener(e,"click",function(){n.open(map,e)})}var geocoder,map,marker,autocomplete;$(window).load(function(){initialize()});</script>
<h2>Telefones</h2>
<input type="text" value="<?= $obj->telefones; ?>" class="form-control input-lg" id="pc" data-role="tagsinput" >

<h2>Endereço</h2>
<form action="io/update.php" method="post" id="envia_endereco">
<br>                              
<div>
  <label>Digite o endereço ou CEP</label>
  <input name="tabela" type="hidden" value="<?= base64_encode('sistema'); ?>">
  <input name="where" type="hidden" value="<?= base64_encode(json_encode(array("ID"=>"1"))); ?>">
  <input type="text" id="endereco" name="endereco" value="<?= $obj->endereco; ?>" class="form-control" maxlength="" required="" placeholder="Digite um local" autocomplete="off">
  <input type="hidden" id="plC" name="telefones">
</div>
<br>
<div class="panel panel-default">
  <div class="panel-body">
    <div id="map-canvas"></div>
  </div>
</div>
<br>
<button class="btn btn-primary">SALVAR</button>
</form>

<script>
  $("#envia_endereco").on('submit',function(e)
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


