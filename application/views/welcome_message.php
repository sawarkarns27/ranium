<!DOCTYPE html>
<html lang="en">
<head>
  <title>Asteroids - Neo</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

  <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css"
         rel = "stylesheet">
   <script src = "https://code.jquery.com/jquery-1.10.2.js"></script>
      <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>


</head>
<body>
<style type="text/css">
	.loader{
		position: absolute;
    	opacity: 0.3;
	    margin-left: 42%;
	    margin-top: 13%;
	}
	@media screen and (min-width: 768px)
	{
		.jumbotron {
		    padding-top: 7px;
		    padding-bottom: 18px;
		}
	}
</style>
<div class="jumbotron text-center container">
	<p class="text-danger"><?php echo $this->session->flashdata('msg');
 ?></p>
  <h1>Asteroids - Neo</h1>
  <p class="text-info">The Feed date limit is only 7 Days.Select dates only with the difference of 7 days.</p>
  <p>
  	<form method="post" action="<?= site_url('Welcome/getObjects')?>">
  	<div class="col-md-4"></div>
  	<div class="form-group col-md-2">
  		<input type="text" name="start_date" class="form-control" id="start_date" placeholder="Start date" readonly="" value="<?php if($this->input->get('start_date')) echo date('d-m-Y',strtotime($this->input->get('start_date')))?>" required>
  	</div>
  	<div class="form-group col-md-2">
  		<input type="text" name="end_date" class="form-control" id="end_date" placeholder="End date" readonly="" value="<?php if($this->input->get('end_date')) echo date('d-m-Y',strtotime($this->input->get('end_date')))?>" required>
  	</div>
  	<div class="col-md-4"></div>
  	<div class="form-group col-md-12">
  		<input type="submit" name="submit" id="submitDates" class="btn btn-success" value="Submit">
  	</div>
  </p> 
</div>
<script type="text/javascript">
	$("#submitDates").click(function(){
		var start_date 	=	$("#start_date").val();
		var end_date 	=	$("#end_date").val();
		var Dstart_date 	=	new Date($("#start_date").val());
		var Dend_date 	=	new Date($("#end_date").val());
		if(start_date == ''){
			alert('Please select start date'); return false;
		}
		if(end_date == ''){
			alert('Please select end date'); return false;
		}
		if(Dstart_date > Dend_date){
			alert('Please select valid dates');return false;
		}

	});

	$("#start_date").datepicker({ 	dateFormat: 'dd-mm-yy' });
	$("#end_date").datepicker({ 	dateFormat: 'dd-mm-yy',
									 });

</script>

<div class="container">
  <div class="row">
    <div class="col-sm-6">
      <center><h3>Fastest Asteroid in km/h</h3>
      <p>Respective Asteroid Id : <?php

      	$data = array(
							'fastestAsteroidId' =>	$this->input->get('fastestAsteroidId'),
							'fastestAsteroidSpeed' =>	$this->input->get('fastestAsteroidSpeed'),
							'closetAsteroidId' =>	$this->input->get('closetAsteroidId'),
							'closestAsteroidDistance' =>	$this->input->get('closestAsteroidDistance'),
						);
		
       if(isset($data) && $data['fastestAsteroidId']) echo $data['fastestAsteroidId'];?></p>
      <p>Speed in km/h : <?php if(isset($data) && $data['fastestAsteroidSpeed']) echo $data['fastestAsteroidSpeed'];?></p></center>
    </div>
    <div class="col-sm-6">
      <center><h3>Closest Asteroid</h3>
      <p>Respective Asteroid Id : <?php if(isset($data) && $data['closetAsteroidId']) echo $data['fastestAsteroidId'];?></p>
      <p>Distance in km : <?php if(isset($data) && $data['closestAsteroidDistance']) echo $data['closestAsteroidDistance'];?></p></center>
    </div>
    <div class="col-sm-4">
    </div>
  </div>
  <div class="row">
  	<?php if($this->input->get('start_date') && $this->input->get('end_date')){?>
  	<div class="col-md-12">
  		<h2 class="loader">Loading Bar chart</h2>
  		<img src="<?php echo base_url();?>/assets/tenor.gif" class="loader">
  		<canvas id="myChart" width="400" height="150"></canvas>
  	</div>
  <?php } ?>
  </div>
</div>
<link rel="stylesheet" type="text/css" href="https://www.chartjs.org/docs/latest/gitbook/gitbook-plugin-chartjs/style.css">
<script type="text/javascript" src="https://www.chartjs.org/docs/latest/gitbook/gitbook-plugin-chartjs/Chart.bundle.js"></script>
<script>

<?php if($this->input->get('start_date') && $this->input->get('end_date')){?>
	var start_date = '<?= $this->input->get('start_date');?>';
	var end_date = '<?= $this->input->get('end_date');?>';
$.ajax({
			type : 'post',
			url : '<?= site_url("Welcome/createDateRange")?>',
			data : {start_date:start_date,end_date:end_date},
			cache: false,
			success : function(response){
				chart(response);
			}
});
<?php } ?>


function chart(arrayData =[])
{
var ctx = document.getElementById('myChart').getContext('2d');
var response = $.parseJSON(arrayData);
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: response.range,
        datasets: [{
            label: 'Asteroid count for each date',
            data: response.element_count,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});
$(".loader").hide();
}
</script>

</body>
</html>
