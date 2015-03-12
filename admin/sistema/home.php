<?php 
	$base = new Conexao(); 
	$sistema = $base->seleciona('sistema',array('ID' =>"1"));
	$api_key = isset($sistema[0]->API_key) ? $sistema[0]->API_key : ""; 
	$site_ids = isset($sistema[0]->site_ids) ? $sistema[0]->site_ids : ""; 
 ?>

<script type="text/javascript" src="js/jquery.peity.min.js"></script>
<div class="Banner-auth" id="auth"></div>
<br>
<div class="col-lg-6">
	<h1 id="nome_site"></h1>
	<span class="bar"></span>
	<br>
	<h2 id="visitas_total">Online</h2>
	<div id="active-users-container"></div>
</div>
<div class="col-lg-6">
	<h2>Total de acessos</h2>
	<br>
	<div class="col-lg-4">
		<span class="circulo">1,2</span>
		<div style="text-align: center;">jan</div>
	</div>
	<div class="col-lg-4">
		<span class="circulo">10,150</span>
		<div style="text-align: center;">fev</div>
	</div>
	<div class="col-lg-4">
		<span class="circulo">20,40</span>
		<div style="text-align: center;">mar</div>
	</div>
</div>
<script>
  (function(w,d,s,g,js,fjs){
    g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(cb){this.q.push(cb)}};
    js=d.createElement(s);fjs=d.getElementsByTagName(s)[0];
    js.src='https://apis.google.com/js/platform.js';
    fjs.parentNode.insertBefore(js,fjs);js.onload=function(){g.load('analytics')};
  }(window,document,'script'));
</script>

<script src="js/Chart.min.js"></script>
<script src="js/moment.min.js"></script>

<!-- Include the ViewSelector2 component script. -->
<script src="js/view-selector2.js"></script>

<!-- Include the DateRangeSelector component script. -->
<script src="js/date-range-selector.js"></script>

<!-- Include the ActiveUsers component script. -->
<script src="js/active-users.js"></script>

<script type="text/javascript">
gapi.analytics.ready(function()
{
	//https://ga-dev-tools.appspot.com/explorer/
	var codigo = "<?= $api_key; ?>";
	var site_idS = "<?= $site_ids; ?>";
	var autorizacao = { container: 'auth',clientid: codigo};
	gapi.analytics.auth.authorize(autorizacao);
	gapi.analytics.auth.on('success', function()
	{
		document.documentElement.classList.add('is-authorized');
	});


	var activeUsers = new gapi.analytics.ext.ActiveUsers({
	    container: 'active-users-container',
	    pollingInterval: 5
	  });

	var dataChart = new gapi.analytics.report.Data({
	  query: {
	    ids: site_idS,
	    metrics: 'ga:sessions',
	    dimensions: 'ga:date',
	    'start-date': '30daysAgo',
	  	'end-date': 'yesterday'
	  }
	});

	dataChart.execute();

	dataChart.on('success', function(response) 
	{

		$('#pi').val()

		var dadosX = [];
		$.each(response.rows, function(index, val) {
			 dadosX[index] = val[1];
		});

		activeUsers.set(response.query).execute();

			$('.bar').text(dadosX);
			$(".bar").peity("bar",{
				  height : '200', 
				  width: '300',
				  fill: ["#2C3E50"]
			})

			$('#nome_site').html(response.profileInfo.profileName);
			console.log(response);
		});		




});

$('.circulo').peity('donut',{
    height : '90', 
  innerRadius : '30',
  fill: ["#c6d9fd", "#2C3E50"],
  width: '90' 
})

</script>


